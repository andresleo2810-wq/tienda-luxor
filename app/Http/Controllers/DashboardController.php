<?php

namespace App\Http\Controllers;

use App\Models\AuditoriaLog;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        Carbon::setLocale('es');
        $hoy = Carbon::today();
        $ayer = Carbon::yesterday();
        $mesInicio = Carbon::now()->startOfMonth();
        $mesAnteriorInicio = Carbon::now()->subMonth()->startOfMonth();
        $mesAnteriorFin = Carbon::now()->subMonth()->endOfMonth();

        // ==== KPIs ====
        $ventasHoy = (float) Venta::whereDate('created_at', $hoy)->sum('total_venta');
        $ventasAyer = (float) Venta::whereDate('created_at', $ayer)->sum('total_venta');

        $ventasMes = (float) Venta::where('created_at', '>=', $mesInicio)->sum('total_venta');
        $ventasMesAnterior = (float) Venta::whereBetween('created_at', [$mesAnteriorInicio, $mesAnteriorFin])->sum('total_venta');

        $stockBajo = Producto::where('estado', true)
            ->whereColumn('stock_actual', '<=', 'stock_minimo')->count();

        $pedidosPendientes = Pedido::where('estado', 'Pendiente')->count();

        $variacionHoy = $this->variacion($ventasHoy, $ventasAyer);
        $variacionMes = $this->variacion($ventasMes, $ventasMesAnterior);

        // ==== Ventas últimos 7 días ====
        $labels7 = [];
        $datos7 = [];
        for ($i = 6; $i >= 0; $i--) {
            $dia = Carbon::today()->subDays($i);
            $labels7[] = ucfirst($dia->isoFormat('ddd D'));
            $datos7[] = (float) Venta::whereDate('created_at', $dia)->sum('total_venta');
        }

        // ==== Métodos de pago (mes actual) ====
        $pagosRaw = Venta::where('created_at', '>=', $mesInicio)
            ->select('metodo_pago', DB::raw('COUNT(*) as total'))
            ->groupBy('metodo_pago')
            ->pluck('total', 'metodo_pago');

        $totalPagos = $pagosRaw->sum() ?: 1;
        $pagos = [
            'Efectivo' => round(($pagosRaw['Efectivo'] ?? 0) / $totalPagos * 100),
            'Tarjeta' => round(($pagosRaw['Tarjeta'] ?? 0) / $totalPagos * 100),
            'Transferencia' => round(($pagosRaw['Transferencia'] ?? 0) / $totalPagos * 100),
        ];

        // ==== Top 5 productos del mes ====
        $topRaw = DB::table('detalle_ventas')
            ->join('productos', 'productos.id', '=', 'detalle_ventas.id_producto')
            ->join('ventas', 'ventas.id', '=', 'detalle_ventas.id_venta')
            ->where('ventas.created_at', '>=', $mesInicio)
            ->select('productos.nombre_producto', DB::raw('SUM(detalle_ventas.cantidad) as unidades'))
            ->groupBy('productos.nombre_producto')
            ->orderByDesc('unidades')
            ->limit(5)
            ->get();

        $maxUnidades = $topRaw->max('unidades') ?: 1;
        $topProductos = $topRaw->map(fn($p) => [
            'nombre' => $p->nombre_producto,
            'unidades' => $p->unidades . ' unidades',
            'porcentaje' => round($p->unidades / $maxUnidades * 100),
        ]);

        // ==== Actividad reciente (auditoría real) ====
        $actividades = AuditoriaLog::latest('created_at')
            ->limit(6)
            ->get()
            ->map(function ($log) {
                $color = 'bg-amber-400';
                if (str_contains($log->descripcion ?? '', 'VOZ')) $color = 'bg-emerald-400';
                elseif (($log->modulo ?? '') === 'Pedidos') $color = 'bg-blue-400';
                elseif (($log->accion ?? '') === 'Anular') $color = 'bg-red-400';
                elseif (($log->accion ?? '') === 'Crear') $color = 'bg-emerald-400';

                return [
                    'tipo' => ($log->accion ?? '') . ' · ' . ($log->modulo ?? ''),
                    'detalle' => ($log->descripcion ?? '') . ' · ' . optional($log->created_at)->diffForHumans(),
                    'valor' => $log->usuario_nombre ?? 'Sistema',
                    'color' => $color,
                ];
            });

        // ==== Saludo ====
        $hora = Carbon::now()->hour;
        $saludo = $hora < 12 ? 'Buenos días' : ($hora < 19 ? 'Buenas tardes' : 'Buenas noches');

        return view('dashboard', compact(
            'ventasHoy', 'ventasMes', 'stockBajo', 'pedidosPendientes',
            'variacionHoy', 'variacionMes',
            'labels7', 'datos7', 'pagos', 'topProductos', 'actividades', 'saludo'
        ));
    }

    private function variacion($actual, $anterior)
    {
        if ($anterior <= 0) return $actual > 0 ? 100.0 : 0.0;
        return round((($actual - $anterior) / $anterior) * 100, 1);
    }
}