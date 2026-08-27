@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-8 flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
    <div>
        <span class="text-xs text-luxor-muted">
            {{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
        </span>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight">
            {{ $saludo }}, {{ explode(' ', Auth::user()->nombre_completo)[0] }}
        </h1>
        <p class="mt-1 text-sm text-luxor-muted">Este es el resumen de tu negocio hoy.</p>
    </div>
    <a href="{{ route('ventas.create') }}"
       class="inline-flex items-center justify-center gap-2 rounded-lg bg-luxor-accent px-4 py-3 text-sm font-semibold text-white hover:bg-luxor-accentDark">
        <i class="bi bi-plus-lg"></i> Nueva venta
    </a>
</div>

<!-- KPI CARDS -->
<div class="mb-5 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div class="rounded-xl border border-luxor-border bg-luxor-surface p-5 shadow-sm">
        <div class="flex items-center justify-between text-xs text-luxor-muted">
            <span>Ventas hoy</span>
            <span class="rounded-lg bg-emerald-100 px-3 py-2 text-emerald-600"><i class="bi bi-cash"></i></span>
        </div>
        <strong class="mt-4 block text-2xl">$ {{ number_format($ventasHoy, 0) }}</strong>
        <div class="mt-3 text-xs {{ $variacionHoy >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
            {{ $variacionHoy >= 0 ? '↑' : '↓' }} {{ number_format(abs($variacionHoy), 1) }}%
            <span class="text-luxor-muted">vs. ayer</span>
        </div>
    </div>

    <div class="rounded-xl border border-luxor-border bg-luxor-surface p-5 shadow-sm">
        <div class="flex items-center justify-between text-xs text-luxor-muted">
            <span>Ventas del mes</span>
            <span class="rounded-lg bg-blue-100 px-3 py-2 text-blue-600"><i class="bi bi-graph-up"></i></span>
        </div>
        <strong class="mt-4 block text-2xl">$ {{ number_format($ventasMes, 0) }}</strong>
        <div class="mt-3 text-xs {{ $variacionMes >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
            {{ $variacionMes >= 0 ? '↑' : '↓' }} {{ number_format(abs($variacionMes), 1) }}%
            <span class="text-luxor-muted">vs. mes anterior</span>
        </div>
    </div>

    <div class="rounded-xl border border-luxor-border bg-luxor-surface p-5 shadow-sm">
        <div class="flex items-center justify-between text-xs text-luxor-muted">
            <span>Stock bajo</span>
            <span class="rounded-lg bg-amber-100 px-3 py-2 text-amber-600"><i class="bi bi-exclamation-triangle"></i></span>
        </div>
        <strong class="mt-4 block text-2xl">{{ $stockBajo }} productos</strong>
        <div class="mt-3 text-xs text-luxor-muted">Requieren reposición</div>
    </div>

    <div class="rounded-xl border border-luxor-border bg-luxor-surface p-5 shadow-sm">
        <div class="flex items-center justify-between text-xs text-luxor-muted">
            <span>Pedidos pendientes</span>
            <span class="rounded-lg bg-purple-100 px-3 py-2 text-purple-600"><i class="bi bi-truck"></i></span>
        </div>
        <strong class="mt-4 block text-2xl">{{ $pedidosPendientes }} pedidos</strong>
        <div class="mt-3 text-xs text-luxor-muted">Por recibir</div>
    </div>
</div>

<!-- GRÁFICAS -->
<div class="mb-5 grid grid-cols-1 gap-5 xl:grid-cols-[1.45fr_1fr]">
    <div class="min-w-0 overflow-hidden rounded-xl border border-luxor-border bg-luxor-surface p-5">
        <div class="mb-5">
            <h2 class="font-semibold">Ventas últimos 7 días</h2>
            <p class="mt-1 text-xs text-luxor-muted">Rendimiento de ventas por día</p>
        </div>
        <div class="relative h-64 sm:h-72"><canvas id="salesChart"></canvas></div>
    </div>
        <div class="min-w-0 overflow-hidden rounded-xl border border-luxor-border bg-luxor-surface p-5">
        <div class="mb-5">
            <h2 class="font-semibold">Métodos de pago</h2>
            <p class="mt-1 text-xs text-luxor-muted">Distribución del mes</p>
        </div>
        <div class="relative mx-auto h-64 max-w-sm sm:h-72"><canvas id="paymentChart"></canvas></div>
    </div>
</div>

<!-- TOP 5 -->
<div class="mb-5 rounded-xl border border-luxor-border bg-luxor-surface p-5">
    <div class="mb-5 flex items-start justify-between gap-3">
        <div>
            <h2 class="font-semibold">Top 5 productos más vendidos</h2>
            <p class="mt-1 text-xs text-luxor-muted">Unidades vendidas este mes</p>
        </div>
        <a href="{{ route('productos.index') }}" class="whitespace-nowrap text-xs text-luxor-accentDark hover:underline">Ver todos →</a>
    </div>
    <div class="space-y-5">
        @forelse($topProductos as $index => $producto)
        <div class="flex items-center gap-4">
            <span class="w-6 text-sm font-bold text-luxor-accentDark">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
            <div class="min-w-0 flex-1">
                <div class="mb-2 flex justify-between gap-3 text-xs">
                    <strong class="truncate">{{ $producto['nombre'] }}</strong>
                    <span class="whitespace-nowrap text-luxor-muted">{{ $producto['unidades'] }}</span>
                </div>
                <div class="h-1.5 overflow-hidden rounded-full bg-luxor-surface2">
                    <div class="h-full rounded-full bg-luxor-accent" style="width: {{ $producto['porcentaje'] }}%"></div>
                </div>
            </div>
        </div>
        @empty
        <p class="text-sm text-luxor-muted">Aún no hay ventas este mes.</p>
        @endforelse
    </div>
</div>

<!-- ACTIVIDAD RECIENTE -->
<div class="rounded-xl border border-luxor-border bg-luxor-surface p-5">
    <div class="mb-5 flex items-center justify-between gap-3">
        <div>
            <h2 class="font-semibold">Actividad reciente</h2>
            <p class="mt-1 text-xs text-luxor-muted">Últimos movimientos en tu tienda</p>
        </div>
        @if(Auth::user()->rol->nombre_rol == 'Administrador')
        <a href="{{ route('auditoria.index') }}" class="whitespace-nowrap text-xs text-luxor-accentDark hover:underline">Ver actividad →</a>
        @endif
    </div>
    <div class="divide-y divide-luxor-border">
        @foreach($actividades as $actividad)
        <div class="flex items-center gap-4 py-4 first:pt-0 last:pb-0">
            <span class="h-2.5 w-2.5 shrink-0 rounded-full {{ $actividad['color'] }}"></span>
            <div class="min-w-0 flex-1">
                <strong class="block text-sm">{{ $actividad['tipo'] }}</strong>
                <small class="block text-xs text-luxor-muted">{{ $actividad['detalle'] }}</small>
            </div>
            <strong class="hidden text-xs text-luxor-muted sm:block">{{ $actividad['valor'] }}</strong>
        </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    const gridColor = getComputedStyle(document.documentElement).getPropertyValue('--lx-border').trim() || '#DCE6DD';
    const mutedColor = '#708178';

    // Formato inteligente: $850K o $1.9M
    function formatoEje(v) {
        if (v >= 1000000) return '$' + (v / 1000000).toFixed(1) + 'M';
        if (v >= 1000) return '$' + Math.round(v / 1000) + 'K';
        return '$' + v;
    }

    new Chart(document.getElementById('salesChart'), {
        type: 'bar',
        data: {
            labels: @json($labels7),
            datasets: [{
                label: 'Ventas',
                data: @json($datos7),
                backgroundColor: '#6E9F78',
                borderRadius: 7,
                borderSkipped: false,
                maxBarThickness: 34
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => '$ ' + new Intl.NumberFormat('es-CO').format(ctx.raw)
                    }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { color: mutedColor, maxRotation: 0, autoSkip: true } },
                y: {
                    beginAtZero: true,
                    grid: { color: gridColor },
                    ticks: { color: mutedColor, callback: v => formatoEje(v) }
                }
            }
        }
    });

    new Chart(document.getElementById('paymentChart'), {
        type: 'doughnut',
        data: {
            labels: ['Efectivo', 'Tarjeta', 'Transferencia'],
            datasets: [{
                data: [
                    {{ $pagos['Efectivo'] ?? 0 }},
                    {{ $pagos['Tarjeta'] ?? 0 }},
                    {{ $pagos['Transferencia'] ?? 0 }}
                ],
                backgroundColor: ['#6E9F78', '#7097A7', '#8E82A6'],
                borderWidth: 0,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: mutedColor, padding: 14, usePointStyle: true, pointStyle: 'circle', boxWidth: 8 }
                }
            }
        }
    });
</script>
@endpush