@extends('layouts.app')

@section('title', 'Caja')

@section('content')
@php
    $abiertas = \App\Models\Caja::where('estado', 'Abierta')->count();
    $cerradasHoy = \App\Models\Caja::where('estado', 'Cerrada')->whereDate('fecha_cierre', now()->toDateString())->count();
    $recaudoHoy = \App\Models\Venta::where('metodo_pago', 'Efectivo')->where('estado', 'Completada')->whereDate('created_at', now()->toDateString())->sum('total_venta');
@endphp

<div class="mb-2 text-xs text-luxor-muted">Gestión / Caja</div>
<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-4xl font-semibold tracking-tight">Caja</h1>
        <p class="mt-2 text-sm text-luxor-muted">Apertura y cierre de caja con control de diferencias.</p>
    </div>
    <a href="{{ route('cajas.create') }}"
       class="btn-luxor inline-flex items-center gap-2 rounded-full bg-luxor-accent px-5 py-2.5 text-sm font-semibold text-white hover:bg-luxor-accentDark">
        <i class="bi bi-unlock"></i> Abrir Caja
    </a>
</div>

{{-- KPIs --}}
<div class="mb-5 grid gap-4 sm:grid-cols-3">
    <div class="rounded-xl border border-luxor-border bg-luxor-surface p-5 shadow-sm">
        <div class="flex items-center justify-between text-xs text-luxor-muted">
            <span>Cajas abiertas</span>
            <span class="grid h-9 w-9 place-items-center rounded-lg bg-emerald-500/15 text-emerald-500"><i class="bi bi-unlock"></i></span>
        </div>
        <strong class="mt-3 block text-2xl">{{ $abiertas }}</strong>
    </div>
    <div class="rounded-xl border border-luxor-border bg-luxor-surface p-5 shadow-sm">
        <div class="flex items-center justify-between text-xs text-luxor-muted">
            <span>Cerradas hoy</span>
            <span class="grid h-9 w-9 place-items-center rounded-lg bg-blue-500/15 text-blue-500"><i class="bi bi-lock"></i></span>
        </div>
        <strong class="mt-3 block text-2xl">{{ $cerradasHoy }}</strong>
    </div>
    <div class="rounded-xl border border-luxor-border bg-luxor-surface p-5 shadow-sm">
        <div class="flex items-center justify-between text-xs text-luxor-muted">
            <span>Efectivo vendido hoy</span>
            <span class="grid h-9 w-9 place-items-center rounded-lg bg-amber-500/15 text-amber-500"><i class="bi bi-cash-stack"></i></span>
        </div>
        <strong class="mt-3 block text-2xl text-emerald-500">$ {{ number_format($recaudoHoy, 0) }}</strong>
    </div>
</div>

{{-- Tabla --}}
<div class="rounded-xl border border-luxor-border bg-luxor-surface shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-luxor-surface2 text-left text-xs text-luxor-muted">
                    <th class="px-5 py-3 font-semibold">#</th>
                    <th class="px-5 py-3 font-semibold">Cajero</th>
                    <th class="px-5 py-3 font-semibold">Apertura</th>
                    <th class="px-5 py-3 font-semibold">Monto final</th>
                    <th class="px-5 py-3 font-semibold">Diferencia</th>
                    <th class="px-5 py-3 font-semibold">Cierre</th>
                    <th class="px-5 py-3 font-semibold">Monto final</th>
                    <th class="px-5 py-3 font-semibold">Estado</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($cajas as $caja)
                <tr class="border-t border-luxor-border hover:bg-luxor-surface2/50">
                    <td class="px-5 py-4"><strong>#{{ $caja->id }}</strong></td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-luxor-surface2 text-xs font-bold text-luxor-muted">
                                {{ strtoupper(substr(optional($caja->usuario)->nombre_completo ?? 'S', 0, 1)) }}
                            </span>
                            <strong>{{ optional($caja->usuario)->nombre_completo ?? '—' }}</strong>
                        </div>
                    </td>
                    <td class="whitespace-nowrap px-5 py-4 text-luxor-muted">{{ optional($caja->fecha_apertura)->format('d/m/Y H:i') }}</td>
                    <td class="whitespace-nowrap px-5 py-4 font-semibold">$ {{ number_format($caja->monto_inicial, 0) }}</td>
                    <td class="whitespace-nowrap px-5 py-4 text-luxor-muted">{{ optional($caja->fecha_cierre)->format('d/m/Y H:i') ?? '—' }}</td>
                                        <td class="whitespace-nowrap px-5 py-4 font-semibold">
                        @if($caja->monto_final_cierre !== null)
                            $ {{ number_format($caja->monto_final_cierre, 0) }}
                        @else
                            <span class="text-luxor-muted">—</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-5 py-4">
                        @if($caja->estado === 'Cerrada')
                            @if($caja->diferencia == 0)
                                <span class="rounded-full bg-emerald-500/15 px-3 py-1 text-xs text-emerald-500">✅ Cuadrada</span>
                            @elseif($caja->diferencia > 0)
                                <span class="rounded-full bg-blue-500/15 px-3 py-1 text-xs text-blue-500">Sobrante + $ {{ number_format($caja->diferencia, 0) }}</span>
                            @else
                                <span class="rounded-full bg-red-500/15 px-3 py-1 text-xs text-red-500">Faltante - $ {{ number_format(abs($caja->diferencia), 0) }}</span>
                            @endif
                        @else
                            <span class="text-luxor-muted">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        @if($caja->estado == 'Abierta')
                            <span class="rounded-full bg-emerald-500/15 px-3 py-1 text-xs text-emerald-500">Abierta</span>
                        @else
                            <span class="rounded-full bg-luxor-surface2 px-3 py-1 text-xs text-luxor-muted">Cerrada</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-right">
                        @if($caja->estado == 'Abierta')
                        <a href="{{ route('cajas.cerrar.form', $caja->id) }}"
                           class="inline-flex items-center gap-2 rounded-full bg-luxor-danger px-4 py-2 text-xs font-semibold text-white hover:opacity-90">
                            <i class="bi bi-lock"></i> Cerrar
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-5 py-12 text-center text-luxor-muted">
                        <i class="bi bi-wallet2 display-6"></i><br>No hay registros de caja
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($cajas, 'links'))
    <div class="p-4">{{ $cajas->links() }}</div>
    @endif
</div>
@endsection