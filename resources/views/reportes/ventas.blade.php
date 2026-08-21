@extends('layouts.app')

@section('title', 'Reportes')

@section('content')
@php
    $ventas = $ventas ?? collect();
    $lista = $ventas instanceof \Illuminate\Pagination\LengthAwarePaginator ? $ventas->getCollection() : $ventas;
    $total = $lista->sum('total_venta');
    $ticket = $lista->count() ? $total / $lista->count() : 0;
    $porMetodo = $lista->groupBy('metodo_pago')->map(fn($v) => $v->sum('total_venta'));
@endphp

<div class="mb-2 text-xs text-luxor-muted">Gestión / Reportes</div>
<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-4xl font-semibold tracking-tight">Reporte de Ventas</h1>
        <p class="mt-2 text-sm text-luxor-muted">Consulta el rendimiento por rango de fechas.</p>
    </div>
    <button onclick="window.print()"
            class="btn-luxor inline-flex items-center gap-2 rounded-full bg-luxor-accent px-5 py-2.5 text-sm font-semibold text-white hover:bg-luxor-accentDark">
        <i class="bi bi-printer"></i> Imprimir
    </button>
</div>

{{-- Filtro de fechas --}}
<form method="GET" action="{{ route('reportes.ventas') }}" class="mb-5 rounded-xl border border-luxor-border bg-luxor-surface p-5 shadow-sm">
    <div class="flex flex-wrap items-end gap-4">
        <div>
            <label class="mb-2 block text-sm font-medium">Desde</label>
            <input type="date" name="desde" value="{{ request('desde', now()->startOfMonth()->format('Y-m-d')) }}"
                   class="rounded-lg border border-luxor-border bg-luxor-surface2 px-4 py-2.5 text-sm outline-none focus:border-luxor-accent">
        </div>
        <div>
            <label class="mb-2 block text-sm font-medium">Hasta</label>
            <input type="date" name="hasta" value="{{ request('hasta', now()->format('Y-m-d')) }}"
                   class="rounded-lg border border-luxor-border bg-luxor-surface2 px-4 py-2.5 text-sm outline-none focus:border-luxor-accent">
        </div>
        <button type="submit"
                class="btn-luxor inline-flex items-center gap-2 rounded-full bg-luxor-accent px-6 py-2.5 text-sm font-semibold text-white hover:bg-luxor-accentDark">
            <i class="bi bi-funnel"></i> Filtrar
        </button>
    </div>
</form>

{{-- KPIs --}}
<div class="mb-5 grid gap-4 sm:grid-cols-3">
    <div class="rounded-xl border border-luxor-border bg-luxor-surface p-5 shadow-sm">
        <span class="text-xs text-luxor-muted">Total vendido</span>
        <strong class="mt-2 block text-2xl text-emerald-500">$ {{ number_format($total, 0) }}</strong>
    </div>
    <div class="rounded-xl border border-luxor-border bg-luxor-surface p-5 shadow-sm">
        <span class="text-xs text-luxor-muted">N° de ventas</span>
        <strong class="mt-2 block text-2xl">{{ $lista->count() }}</strong>
    </div>
    <div class="rounded-xl border border-luxor-border bg-luxor-surface p-5 shadow-sm">
        <span class="text-xs text-luxor-muted">Ticket promedio</span>
        <strong class="mt-2 block text-2xl">$ {{ number_format($ticket, 0) }}</strong>
    </div>
</div>

{{-- Por método de pago --}}
<div class="mb-5 grid gap-4 sm:grid-cols-3">
    @foreach(['Efectivo' => 'emerald', 'Tarjeta' => 'blue', 'Transferencia' => 'purple'] as $metodo => $color)
    <div class="rounded-xl border border-luxor-border bg-luxor-surface p-5 shadow-sm">
        <div class="flex items-center justify-between text-xs text-luxor-muted">
            <span>{{ $metodo }}</span>
            <span class="rounded-lg bg-{{ $color }}-500/15 px-2 py-1 text-{{ $color }}-500"><i class="bi bi-credit-card"></i></span>
        </div>
        <strong class="mt-2 block text-xl">$ {{ number_format($porMetodo[$metodo] ?? 0, 0) }}</strong>
    </div>
    @endforeach
</div>

{{-- Tabla --}}
<div class="rounded-xl border border-luxor-border bg-luxor-surface shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-luxor-surface2 text-left text-xs text-luxor-muted">
                    <th class="px-5 py-3 font-semibold">#</th>
                    <th class="px-5 py-3 font-semibold">Fecha</th>
                    <th class="px-5 py-3 font-semibold">Cajero</th>
                    <th class="px-5 py-3 font-semibold">Método</th>
                    <th class="px-5 py-3 font-semibold">Estado</th>
                    <th class="px-5 py-3 text-right font-semibold">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lista as $venta)
                <tr class="border-t border-luxor-border">
                    <td class="px-5 py-3"><strong>#{{ $venta->id }}</strong></td>
                    <td class="whitespace-nowrap px-5 py-3 text-luxor-muted">{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-5 py-3">{{ optional($venta->usuario)->nombre_completo }}</td>
                    <td class="px-5 py-3"><span class="rounded-full bg-luxor-surface2 px-3 py-1 text-xs">{{ $venta->metodo_pago }}</span></td>
                    <td class="px-5 py-3">
                        @if($venta->estado == 'Completada')
                            <span class="rounded-full bg-emerald-500/15 px-3 py-1 text-xs text-emerald-500">Completada</span>
                        @else
                            <span class="rounded-full bg-red-500/15 px-3 py-1 text-xs text-red-500">{{ $venta->estado }}</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-5 py-3 text-right font-semibold">$ {{ number_format($venta->total_venta, 0) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center text-luxor-muted">
                        <i class="bi bi-graph-up display-6"></i><br>No hay ventas en el rango seleccionado
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($ventas, 'links'))
    <div class="p-4">{{ $ventas->links() }}</div>
    @endif
</div>
@endsection