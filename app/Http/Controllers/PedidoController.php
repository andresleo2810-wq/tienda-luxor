<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Proveedor;
use App\Models\Producto;
use App\Models\AuditoriaLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PedidoController extends Controller
{
    public function index()
    {
        $pedidos = Pedido::with(['proveedor', 'usuario'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pedidos.index', compact('pedidos'));
    }

    public function create()
    {
        $proveedores = Proveedor::where('estado', true)->orderBy('nombre')->get();
        $productos = Producto::where('estado', true)->orderBy('nombre_producto')->get();

        return view('pedidos.create', compact('proveedores', 'productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_proveedor' => 'required|exists:proveedores,id',
            'fecha_pedido' => 'required|date',
            'observaciones' => 'nullable|string',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.costo' => 'required|numeric|min:0'
        ], [
            'id_proveedor.required' => 'Seleccione un proveedor',
            'productos.required' => 'Agregue al menos un producto'
        ]);

        try {
            DB::beginTransaction();

            $total = 0;
            $detalles = [];

            foreach ($request->productos as $item) {
                if (empty($item['id'])) continue;

                $producto = Producto::find($item['id']);
                $subtotal = $item['cantidad'] * $item['costo'];
                $total += $subtotal;

                $detalles[] = [
                    'id_producto' => $producto->id,
                    'cantidad' => $item['cantidad'],
                    'costo_unitario' => $item['costo'],
                    'subtotal' => $subtotal
                ];
            }

            if (empty($detalles)) {
                throw new \Exception('Agregue al menos un producto al pedido');
            }

            $pedido = Pedido::create([
                'id_proveedor' => $request->id_proveedor,
                'id_usuario' => auth()->id(),
                'total_pedido' => $total,
                'estado' => 'Pendiente',
                'fecha_pedido' => $request->fecha_pedido,
                'observaciones' => $request->observaciones
            ]);

            foreach ($detalles as $detalle) {
                $pedido->detalles()->create($detalle);
            }

            DB::commit();

            AuditoriaLog::registrar('Crear', 'Pedidos', "Pedido #{$pedido->id} creado");

            return redirect()->route('pedidos.index')
                ->with('success', "Pedido #{$pedido->id} creado exitosamente");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $pedido = Pedido::with(['proveedor', 'usuario', 'detalles.producto'])->findOrFail($id);

                // 🤖 Si la IA analizó la factura, calcular coincidencias + productos nuevos
                $analisis = null;
        $lineas = [];
        if ($pedido->texto_ocr) {
            $analisis = $this->coincidenciasIA($pedido, $pedido->texto_ocr);
            $lineas = $this->detectarProductosNuevos($pedido->texto_ocr);
        }

        return view('pedidos.show', compact('pedido', 'analisis', 'lineas'));
    }

    /**
     * 📷 RECIBIR PEDIDO: foto de factura + IA + inventario automático
     */
    public function recibir(Request $request, $id)
{
    $pedido = Pedido::with('detalles')->findOrFail($id);

    if ($pedido->estado !== 'Pendiente') {
        return redirect()->route('pedidos.index')
            ->with('error', 'Solo se pueden recibir pedidos pendientes');
    }

    $request->validate([
        'factura' => 'required|image|mimes:jpg,jpeg,png,webp|max:20480',
    ], [
        'factura.required' => 'Debe adjuntar la foto de la factura',
        'factura.image' => 'El archivo debe ser una imagen',
        'factura.max' => 'La imagen no debe superar 20MB'
    ]);

    try {
        DB::beginTransaction();

        // 1. Guardar foto original
        $rutaFactura = $request->file('factura')->store('facturas', 'public');

        // 2. IA analiza (sin tocar inventario todavía)
        $textoOCR = $this->extraerTextoConIA(storage_path('app/public/' . $rutaFactura));

        // 3. Guardar factura + texto IA (pedido sigue Pendiente)
        $pedido->update([
            'ruta_factura' => $rutaFactura,
            'texto_ocr' => $textoOCR,
        ]);

        DB::commit();

        AuditoriaLog::registrar('Recibir', 'Pedidos', 
            "Pedido #{$pedido->id}: factura recibida y analizada por IA, pendiente de verificación");

        return redirect()->route('pedidos.verificar', $pedido->id)
            ->with('success', '🤖 Factura analizada. Verifique las cantidades antes de actualizar el inventario.');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Error al recibir: ' . $e->getMessage());
    }
}

    public function cancelar($id)
    {
        $pedido = Pedido::findOrFail($id);

        if ($pedido->estado !== 'Pendiente') {
            return redirect()->route('pedidos.index')
                ->with('error', 'Solo se pueden cancelar pedidos pendientes');
        }

        $pedido->update(['estado' => 'Cancelado']);

        AuditoriaLog::registrar('Cancelar', 'Pedidos', "Pedido #{$pedido->id} cancelado");

        return redirect()->route('pedidos.index')
            ->with('success', "Pedido #{$pedido->id} cancelado");
    }

    /* ============================================
        MÉTODOS DE INTELIGENCIA ARTIFICIAL (OCR)
       ============================================ */

    
    private function extraerTextoConIA($rutaImagen)
    {
        try {
            // Comprimir para pasar el límite gratis de 1MB
            $datosComprimidos = $this->comprimirParaOCR($rutaImagen);

            if (!$datosComprimidos) return null;

            $base64 = base64_encode($datosComprimidos);

            $response = Http::asForm()->timeout(30)->post('https://api.ocr.space/parse/image', [
                'apikey' => 'helloworld',   //  Key gratuita
                'language' => 'spa',        // Español
                'base64Image' => 'data:image/jpeg;base64,' . $base64,
                'isOverlayRequired' => 'false',
            ]);

            $texto = collect($response->json('ParsedResults'))
                ->pluck('ParsedText')
                ->implode(' ');

            return trim($texto) ?: null;

        } catch (\Exception $e) {
            return null;   // Si falla la IA, no rompe el proceso
        }
    }

    /**
     * 🗜️ Comprime/redimensiona la imagen para el OCR
     */
    private function comprimirParaOCR($rutaImagen)
    {
        $info = @getimagesize($rutaImagen);
        if (!$info) return null;

        switch ($info['mime']) {
            case 'image/jpeg': $src = @imagecreatefromjpeg($rutaImagen); break;
            case 'image/png':  $src = @imagecreatefrompng($rutaImagen); break;
            case 'image/webp': $src = @imagecreatefromwebp($rutaImagen); break;
            default: return null;
        }
        if (!$src) return null;

        $w = imagesx($src);
        $h = imagesy($src);
        $max = 1600;

        if ($w > $max) {
            $nw = $max;
            $nh = intval($h * $max / $w);
            $dst = imagecreatetruecolor($nw, $nh);
            imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
            imagedestroy($src);
            $src = $dst;
        }

        $calidad = 85;
        do {
            ob_start();
            imagejpeg($src, null, $calidad);
            $data = ob_get_clean();
            $calidad -= 10;
        } while (strlen($data) > 700 * 1024 && $calidad > 30);

        imagedestroy($src);

        return $data;
    }

    /**
     * Compara el texto de la IA con los productos del pedido
     */
        private function coincidenciasIA($pedido, $texto)
    {
        $textoNormalizado = $this->normalizar($texto);
        $genericas = ['750ML', '375ML', '1000ML', '300ML', 'LITRO', 'PREMIUM', 'RESERVA', 'CLASICO', 'ORIGINAL', 'UNIDAD', 'CAJA'];
        $resultados = [];

        foreach ($pedido->detalles as $detalle) {
            $nombre = $this->normalizar($detalle->producto->nombre_producto);
            $palabras = collect(explode(' ', $nombre))
                ->filter(fn($p) => strlen($p) >= 5 && !in_array($p, $genericas));

            $detectado = $palabras->contains(
                fn($p) => str_contains($textoNormalizado, $p)
            );

            $resultados[] = [
                'producto' => $detalle->producto->nombre_producto,
                'cantidad' => $detalle->cantidad,
                'detectado' => $detectado,
            ];
        }

        return $resultados;
    }

    /**
     * Quita acentos y pasa a mayúsculas
     */
    private function normalizar($texto)
    {
        $texto = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto);
        return strtoupper($texto);
    }
    /**
 * 👁️ Pantalla de verificación: muestra qué detectó la IA
 * y deja ajustar cantidades reales recibidas
 */
    public function verificar($id)
    {
        $pedido = Pedido::with(['proveedor', 'detalles.producto'])->findOrFail($id);

        if ($pedido->estado !== 'Pendiente') {
            return redirect()->route('pedidos.show', $pedido->id);
        }

                $analisis = $pedido->texto_ocr 
            ? $this->coincidenciasIA($pedido, $pedido->texto_ocr) 
            : null;

                $nuevos = $pedido->texto_ocr
            ? array_values(array_filter(
                $this->detectarProductosNuevos($pedido->texto_ocr),
                fn($n) => !($n['existe'] ?? false)
            ))
            : [];
        return view('pedidos.verificar', compact('pedido', 'analisis', 'nuevos'));
    }

    /**
     * ✅ Confirma la recepción con cantidades reales y actualiza inventario
     */
    public function confirmarRecepcion(Request $request, $id)
    {
        $pedido = Pedido::with('detalles')->findOrFail($id);

        if ($pedido->estado !== 'Pendiente') {
            return redirect()->route('pedidos.index')
                ->with('error', 'Este pedido ya fue procesado');
        }

        try {
            DB::beginTransaction();

            $completo = true;
            $totalRecibido = 0;

            foreach ($pedido->detalles as $detalle) {
                // Cantidad que el usuario confirmó (limitada entre 0 y lo pedido)
                $recibida = (int) $request->input('recibido.' . $detalle->id, 0);
                $recibida = max(0, min($recibida, $detalle->cantidad));

                $detalle->update(['cantidad_recibida' => $recibida]);

                // 📦 Solo entra al inventario lo REALMENTE recibido
                if ($recibida > 0) {
                    $producto = Producto::find($detalle->id_producto);
                    $producto->stock_actual += $recibida;
                    $producto->precio_costo = $detalle->costo_unitario;
                    $producto->save();
                    $totalRecibido += $recibida;
                }

                if ($recibida != $detalle->cantidad) {
                    $completo = false;
                }
            }

            $pedido->update([
                'estado' => $completo ? 'Recibido' : 'Parcial',
                'fecha_recepcion' => now(),
            ]);
            // 🆕 Crear productos detectados por la IA que no existían
            $creados = 0;
            if ($request->has('nuevos')) {
                foreach ($request->nuevos as $n) {
                    if (empty($n['crear'])) continue;
                    $nombre = trim($n['nombre'] ?? '');
                    $costo = (float) ($n['costo'] ?? 0);
                    $venta = (float) ($n['venta'] ?? 0);
                    if ($nombre === '' || $costo <= 0) continue;
                    if ($venta <= 0) $venta = round($costo * 1.4);

                    DB::table('productos')->insert([
                        'nombre_producto' => $nombre,
                        'marca' => 'Por clasificar',
                        'categoria' => 'Por clasificar',
                        'precio_costo' => $costo,
                        'precio_venta' => $venta,
                        'stock_actual' => (int) ($n['cantidad'] ?? 0),
                        'stock_minimo' => 5,
                        'estado' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $creados++;
                }
            }
            if ($creados > 0) {
                AuditoriaLog::registrar('Crear', 'Productos',
                    "🤖 La IA creó {$creados} producto(s) desde la factura del pedido #{$pedido->id}");
            }

            DB::commit();

            AuditoriaLog::registrar('Recibir', 'Pedidos', 
                "Pedido #{$pedido->id} confirmado: " . ($completo 
                    ? 'recibido COMPLETO' 
                    : 'recibido PARCIAL con diferencias'));

            return redirect()->route('pedidos.show', $pedido->id)
                ->with('success', $completo 
                    ? "Pedido #{$pedido->id} recibido COMPLETO. Inventario actualizado." 
                    : "Pedido #{$pedido->id} recibido PARCIAL. Inventario actualizado solo con lo confirmado.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
 
    public function facturas()
    {
        $facturas = Pedido::with('proveedor')
            ->whereNotNull('ruta_factura')
            ->where('ruta_factura', '!=', '')
            ->orderBy('fecha_recepcion', 'desc')
            ->get();

        $proveedores = $facturas->pluck('proveedor.nombre')->unique()->values();

        return view('pedidos.facturas', compact('facturas', 'proveedores'));
    }
    public function vozPedido(Request $request)
    {
        $request->validate([
            'id_proveedor' => 'required|exists:proveedores,id',
            'productos'   => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        $proveedor = \App\Models\Proveedor::find($request->id_proveedor);
        $total = 0;
        $detalles = [];

        foreach ($request->productos as $item) {
            $producto = \App\Models\Producto::find($item['id']);
            $sub = $item['cantidad'] * $producto->precio_costo;
            $total += $sub;
            $detalles[] = [
                'id_producto' => $producto->id,
                'cantidad' => $item['cantidad'],
                'costo_unitario' => $producto->precio_costo,
                'subtotal' => $sub,
            ];
        }

        $pedidoId = DB::table('pedidos')->insertGetId([
            'id_proveedor' => $request->id_proveedor,
            'id_usuario' => auth()->id(),
            'fecha_pedido' => now(),
            'estado' => 'Pendiente',
            'total_pedido' => $total,
            'observaciones' => 'Creado por comando de VOZ',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ($detalles as $d) {
            $d['id_pedido'] = $pedidoId;
            DB::table('detalle_pedidos')->insert($d);
        }

        AuditoriaLog::registrar('Crear', 'Pedidos', "Pedido por VOZ #{$pedidoId} a {$proveedor->nombre}");

        return redirect()->route('pedidos.show', $pedidoId)
            ->with('success', "Pedido por voz #{$pedidoId} creado");
    }
        /**
     * 🆕 Detecta en el texto de la IA productos que NO existen en el catálogo
     */    private function detectarProductosNuevos($texto)
    {
        if (!$texto) return [];

        $metadata = ['NIT', 'FACTURA', 'FECHA', 'TOTAL', 'IVA', 'SUBTOTAL', 'VALOR', 'PRECIO', 'CANTIDAD', 'DETALLE', 'TEL', 'DIRECCION', 'CARRERA', 'CALLE', 'CLIENTE', 'VENDEDOR', 'CAJERO', 'RESOLUCION', 'PAGINA', 'PAGO', 'EFECTIVO', 'TARJETA', 'TRANSFERENCIA', 'CAMBIO', 'GRACIAS', 'WWW', 'HTTP', 'SAS', 'LTDA', 'IMPUESTO', 'CONSUMO', 'DOMICILIO', 'USUARIO', 'MESA', 'CARMENZA', 'LATA'];

        $genericas = ['WHISKY', 'BRANDY', 'RON', 'VODKA', 'TEQUILA', 'GINEBRA', 'AGUARDIENTE', 'LICOR', 'CREMA', 'CERVEZA', 'VINO', 'ESPUMOSO', 'CHAMPANA', 'AMARETTO', 'ANIS'];

        $existentes = Producto::pluck('nombre_producto')
            ->map(fn($n) => ' ' . $this->normalizar($n) . ' ');

        $nuevos = [];
        $vistos = [];

        foreach (preg_split('/[\r\n]+/', $texto) as $linea) {
            $linea = trim($linea);
            if (strlen($linea) < 8 || strlen($linea) > 120) continue;

            $mayus = $this->normalizar($linea);

            $esMetadata = false;
            foreach ($metadata as $pal) {
                if (str_contains($mayus, $pal)) { $esMetadata = true; break; }
            }
            if ($esMetadata) continue;
            // 🛡️ REGLA DE ORO: solo es producto si menciona categoría o marca conocida
            $categorias = ['WHISKY', 'BRANDY', 'RON', 'VODKA', 'TEQUILA', 'GINEBRA', 'AGUARDIENTE', 'LICOR', 'CREMA', 'CERVEZA', 'VINO', 'ESPUMOSO', 'CHAMPANA', 'AMARETTO', 'ANIS', 'SANGRIA', 'GUARO', 'BEBIDA', 'ESTUCHE'];
            $marcas = ['JACK', 'DANIELS', 'JOHNNIE', 'WALKER', 'BUCHANAN', 'CHIVAS', 'BALLANTINES', 'PARR', 'SMIRNOFF', 'ABSOLUT', 'TANQUERAY', 'BOMBAY', 'BACARDI', 'HAVANA', 'DOMECQ', 'DOMEQC', 'FUNDADOR', 'BAILEYS', 'AMARULA', 'JAGER', 'HENNESSY', 'BUDWEISER', 'AGUILA', 'POKER', 'CORONA', 'STELLA', 'MARQUES', 'CASILLERO', 'FRONTERA', 'TENESSEE'];
            $esProducto = false;
            foreach (array_merge($categorias, $marcas) as $cat) {
                if (str_contains($mayus, $cat)) { $esProducto = true; break; }
            }
            if (!$esProducto) continue;

            //  Nunca considerar correos ni URLs
            if (str_contains($linea, '@') || str_contains($mayus, '.COM') || str_contains($mayus, 'GMAIL')) continue;

            // 🔢 Extraer TODOS los números
            $cantidad = 1;
            $codigo = '';
            $volumen = '';
            $grado = '';
            $precio = 0;

            // Cantidad: primer número de 1-3 dígitos al inicio
            if (preg_match('/^(\d{1,3})\b/', trim($linea), $cm)) {
                $cantidad = max(1, (int) $cm[1]);
            }

                        // Volumen: 375ml, 750ml, 1.5L o medidas sueltas típicas (X 700)
            if (preg_match('/(\d{3,4})\s*ml/i', $linea, $vm)) {
                $volumen = $vm[1] . 'ml';
            } elseif (preg_match('/(\d+[,\.]?\d*)\s*l\b/i', $linea, $vm)) {
                $volumen = $vm[1] . 'L';
            } elseif (preg_match('/\b(175|200|250|300|375|500|700|750|1000|1500)\b/', $linea, $vm)) {
                $volumen = $vm[1] . 'ml';
            }

            // Grado alcohólico: 14°, 28°, 40°
            if (preg_match('/(\d{2,3})\s*°/', $linea, $gm)) {
                $grado = $gm[1] . '°';
            }

                        // Precio: punto o coma de miles (15.000 / 94,285)
            preg_match_all('/\d{1,3}(?:[.,]\d{3})+(?:[.,]\d{2})?/', $linea, $pm);
            foreach (array_reverse($pm[0]) as $num) {
                $v = (int) str_replace(['.', ','], '', preg_replace('/[.,]\d{2}$/', '', $num));
                if ($v >= 1000 && $v <= 5000000) { $precio = $v; break; }
            }

            // Código: número largo de 8+ dígitos (código de barras o referencia)
            if (preg_match('/\b(\d{8,})\b/', $linea, $cod)) {
                $codigo = $cod[1];
            }

            // Nombre: limpiar números y símbolos
            $nombre = trim(preg_replace('/\d{1,3}(?:\.\d{3})+|\d+|\$|[%*|°]/u', ' ', $linea));
            $nombre = trim(preg_replace('/\s+/', ' ', $nombre), ' xX.-+');
            if (mb_strlen($nombre) < 6) continue;

            // ¿Ya existe?
            $palabras = array_values(array_filter(
                explode(' ', $this->normalizar($nombre)),
                fn($p) => strlen($p) >= 6 && !in_array($p, $genericas)
            ));
            $existe = false;
            foreach ($palabras as $p) {
                foreach ($existentes as $prod) {
                    if (str_contains($prod, $p)) { $existe = true; break 2; }
                }
            }

            $clave = $this->normalizar($nombre);
            if (in_array($clave, $vistos)) continue;
            $vistos[] = $clave;

                       // 📏 El volumen va en el nombre (en licores 375ml ≠ 750ml)
            $nombreFinal = mb_convert_case($nombre, MB_CASE_TITLE, 'UTF-8');
            if ($volumen) {
                $nombreFinal .= ' ' . $volumen;
            }

            $nuevos[] = [
                'nombre' => $nombreFinal,
                'precio' => $precio,
                'existe' => $existe,
                'cantidad' => $cantidad,
                'codigo' => $codigo,
                'volumen' => $volumen,
                'grado' => $grado,
            ];

            if (count($nuevos) >= 15) break;
        }

        return $nuevos;
    }
}