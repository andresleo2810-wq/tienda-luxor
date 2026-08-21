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

        // 🤖 Si la IA analizó la factura, calcular coincidencias
        $analisis = null;
        if ($pedido->texto_ocr) {
            $analisis = $this->coincidenciasIA($pedido, $pedido->texto_ocr);
        }

        return view('pedidos.show', compact('pedido', 'analisis'));
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
        $resultados = [];

        foreach ($pedido->detalles as $detalle) {
            $nombre = $this->normalizar($detalle->producto->nombre_producto);
            $palabras = collect(explode(' ', $nombre))
                ->filter(fn($p) => strlen($p) >= 4);

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

        return view('pedidos.verificar', compact('pedido', 'analisis'));
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
}