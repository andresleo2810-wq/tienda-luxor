<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use App\Models\AuditoriaLog;

class ReporteController extends Controller
{
    public function ventas(Request $request)
    {
        // Rango de fechas (por defecto: mes actual)
        $fecha_inicio = $request->input('fecha_inicio', now()->startOfMonth()->toDateString());
        $fecha_fin = $request->input('fecha_fin', now()->toDateString());

        // ✅ Traer ventas y filtrar en PHP (evita problemas de zona horaria)
        $ventas = Venta::with('usuario')
            ->orderBy('created_at', 'desc')
            ->get()
            ->filter(function ($v) use ($fecha_inicio, $fecha_fin) {
                $fecha = $v->created_at->format('Y-m-d');
                return $fecha >= $fecha_inicio && $fecha <= $fecha_fin;
            })
            ->values();

        // Indicadores
        $total_ventas = $ventas->sum('total_venta');
        $total_transacciones = $ventas->count();
        $ticket_promedio = $total_transacciones > 0 
            ? $total_ventas / $total_transacciones 
            : 0;

        // ✅ Top productos usando los IDs de las ventas filtradas
        $idsVentas = $ventas->pluck('id');

        $topProductos = DetalleVenta::with('producto')
            ->whereIn('id_venta', $idsVentas)
            ->selectRaw('id_producto, SUM(cantidad) as total_vendido, SUM(subtotal) as total_ingresos')
            ->groupBy('id_producto')
            ->orderByDesc('total_vendido')
            ->limit(10)
            ->get();

        return view('reportes.ventas', compact(
            'ventas', 
            'total_ventas', 
            'total_transacciones', 
            'ticket_promedio', 
            'topProductos', 
            'fecha_inicio', 
            'fecha_fin'
        ));
    }
}