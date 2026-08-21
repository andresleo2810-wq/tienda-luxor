<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Producto;
use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\AuditoriaLog;

class VentaController extends Controller
{
    /**
     * Listar ventas
     */
    public function index()
    {
        $ventas = Venta::with(['usuario', 'detalles'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            $productosVoz = Producto::where('estado', true)->get(['id', 'nombre_producto']);
        
        return view('ventas.index', compact('ventas', 'productosVoz'));
    }

    /**
     * Formulario de nueva venta
     */
    public function create()
    {
        $productos = Producto::where('estado', true)
            ->where('stock_actual', '>', 0)
            ->orderBy('nombre_producto')
            ->get();
        
        return view('ventas.create', compact('productos'));
    }



    /**
     * Registrar nueva venta
     */
public function store(Request $request)
{
    $request->validate([
        'productos' => 'required|array|min:1',
        'productos.*.id' => 'required|exists:productos,id',
        'productos.*.cantidad' => 'required|integer|min:1',
        'metodo_pago' => 'required|in:Efectivo,Tarjeta,Transferencia'
    ]);

    try {
        DB::beginTransaction();

        $total = 0;
        $detalles = [];

        foreach ($request->productos as $item) {
            // Saltar filas vacías
            if (empty($item['id'])) {
                continue;
            }
            
            $producto = Producto::find($item['id']);
            
            if (!$producto) {
                throw new \Exception("Producto no encontrado (ID: {$item['id']})");
            }
            
            // Validar stock disponible
            if ($producto->stock_actual < $item['cantidad']) {
                throw new \Exception("Stock insuficiente para {$producto->nombre_producto}. Disponible: {$producto->stock_actual}");
            }

            $subtotal = $producto->precio_venta * $item['cantidad'];
            $total += $subtotal;

            $detalles[] = [
                'id_producto' => $producto->id, 
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $producto->precio_venta,
                'subtotal' => $subtotal
            ];

            // Descontar stock
            $producto->stock_actual -= $item['cantidad'];
            $producto->save();
        }

        if (empty($detalles)) {
            throw new \Exception("Debe agregar al menos un producto a la venta");
        }

        // Crear venta
        $venta = Venta::create([
            'id_usuario' => auth()->id(),
            'total_venta' => $total,
            'metodo_pago' => $request->metodo_pago,
            'estado' => 'Completada'
        ]);

        // Crear detalles
        foreach ($detalles as $detalle) {
            $venta->detalles()->create($detalle);
        }

        DB::commit();
        AuditoriaLog::registrar('Crear', 'Ventas', "Venta #{$venta->id} - Total: $" . number_format($total, 0));

        return redirect()->route('ventas.index')
            ->with('success', "Venta #{$venta->id} registrada exitosamente. Total: $" . number_format($total, 0));

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error en venta: ' . $e->getMessage());
        return back()->with('error', 'Error al registrar venta: ' . $e->getMessage())
            ->withInput();
    }
}

    /**
     * Ver detalle de venta
     */
    public function show($id)
    {
        $venta = Venta::with(['usuario', 'detalles.producto'])->findOrFail($id);
        return view('ventas.show', compact('venta'));
    }

    /**
     * Anular venta (devolver stock)
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $venta = Venta::findOrFail($id);

            // Devolver stock de cada producto
            foreach ($venta->detalles as $detalle) {
                $producto = $detalle->producto;
                $producto->stock_actual += $detalle->cantidad;
                $producto->save();
            }

            // Eliminar detalles y venta
            $venta->detalles()->delete();
            $venta->delete();

            DB::commit();
            AuditoriaLog::registrar('Anular', 'Ventas', "Venta #{$venta->id} anulada");

            return redirect()->route('ventas.index')
                ->with('success', 'Venta anulada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al anular venta: ' . $e->getMessage());
        }
    }
            public function storeVoz(Request $request)
    {
        $request->validate([
            'id_producto' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
            'tipo' => 'required|in:venta,ingreso'
        ]);

        $producto = Producto::findOrFail($request->id_producto);
        $tipo = $request->input('tipo', 'venta');

        //  MODO INGRESO: suma stock y termina
        if ($tipo === 'ingreso') {
            $producto->stock_actual += $request->cantidad;
            $producto->save();

            AuditoriaLog::registrar('Ingreso', 'Productos',
                "🎤 Ingreso por VOZ: +{$request->cantidad} x {$producto->nombre_producto}");

            return redirect()->route('productos.index')
                ->with('success', " Ingreso por voz: +{$request->cantidad} x {$producto->nombre_producto}. Stock actual: {$producto->stock_actual}");
        }

        //  MODO VENTA: código existente
        if ($producto->stock_actual < $request->cantidad) {
            return back()->with('error',
                "Stock insuficiente de {$producto->nombre_producto}. Disponible: {$producto->stock_actual}");
        }

        try {
            DB::beginTransaction();

            $subtotal = $producto->precio_venta * $request->cantidad;

            $venta = Venta::create([
                'id_usuario' => auth()->id(),
                'total_venta' => $subtotal,
                'metodo_pago' => 'Efectivo',
                'estado' => 'Completada',
            ]);

            DetalleVenta::create([
                'id_venta' => $venta->id,
                'id_producto' => $producto->id,
                'cantidad' => $request->cantidad,
                'precio_unitario' => $producto->precio_venta,
                'subtotal' => $subtotal,
            ]);

            $producto->stock_actual -= $request->cantidad;
            $producto->save();

            DB::commit();

            AuditoriaLog::registrar('Crear', 'Ventas',
                "🎤 Venta por VOZ #{$venta->id}: {$request->cantidad} x {$producto->nombre_producto}");

            return redirect()->route('ventas.index')
                ->with('success', "🎤 Venta por voz registrada: {$request->cantidad} x {$producto->nombre_producto}");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    }