@extends('layouts.app')

@section('title', 'Detalle de Venta')

@section('content')
<div class="mb-2 text-xs text-luxor-muted">Gestión / Ventas / #{{ $venta->id }}</div>
<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-4xl font-semibold tracking-tight">Venta #{{ $venta->id }}</h1>
        <p class="mt-2 text-sm text-luxor-muted">{{ $venta->created_at->format('d/m/Y · H:i') }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('ventas.index') }}"
           class="btn-luxor inline-flex items-center gap-2 rounded-full border border-luxor-border bg-luxor-surface px-5 py-2.5 text-sm font-semibold text-luxor-muted hover:text-luxor-text">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
        <button onclick="window.print()"
                class="btn-luxor inline-flex items-center gap-2 rounded-full bg-luxor-accent px-5 py-2.5 text-sm font-semibold text-white hover:bg-luxor-accentDark">
            <i class="bi bi-printer"></i> Imprimir
        </button>
    </div>
</div>

<div class="grid gap-5 xl:grid-cols-[1.6fr_1fr]">
    {{-- ============ PRODUCTOS VENDIDOS ============ --}}
    <div class="rounded-xl border border-luxor-border bg-luxor-surface shadow-sm">
        <div class="flex items-center justify-between border-b p-5" style="border-color: var(--lx-border);">
            <h2 class="font-semibold"><i class="bi bi-receipt text-luxor-accent"></i> Productos vendidos</h2>
            <span class="whitespace-nowrap rounded-full bg-luxor-surface2 px-3 py-1 text-xs">{{ $venta->detalles->count() }} ítem(s)</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-luxor-surface2 text-left text-xs text-luxor-muted">
                        <th class="px-5 py-3 font-semibold">Producto</th>
                        <th class="px-5 py-3 font-semibold">Cantidad</th>
                        <th class="px-5 py-3 font-semibold">Precio unit.</th>
                        <th class="px-5 py-3 text-right font-semibold">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($venta->detalles as $detalle)
                    <tr class="border-t border-luxor-border">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-blue-500/15 text-blue-500">
                                    <i class="bi bi-box-seam"></i>
                                </span>
                                <div>
                                    <strong class="block">{{ $detalle->producto->nombre_producto }}</strong>
                                    <small class="text-xs text-luxor-muted">{{ $detalle->producto->categoria ?? '' }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4"><strong>{{ $detalle->cantidad }}</strong> <span class="text-xs text-luxor-muted">un.</span></td>
                        <td class="px-5 py-4 whitespace-nowrap text-luxor-muted">$ {{ number_format($detalle->precio_unitario, 0) }}</td>
                        <td class="px-5 py-4 whitespace-nowrap text-right font-semibold">$ {{ number_format($detalle->subtotal, 0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t border-luxor-border" style="background: var(--lx-surface2);">
                        <td colspan="3" class="px-5 py-4 text-right text-sm font-semibold text-luxor-muted">TOTAL</td>
                        <td class="whitespace-nowrap px-5 py-4 text-right text-lg font-bold text-emerald-500">$ {{ number_format($venta->total_venta, 0) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- ============ INFORMACIÓN ============ --}}
    <div class="space-y-5">
        <div class="rounded-xl border border-luxor-border bg-luxor-surface p-5 shadow-sm">
            <h2 class="mb-5 font-semibold"><i class="bi bi-info-circle text-luxor-accent"></i> Información</h2>
            <div class="space-y-4 text-sm">
                <div class="flex items-center gap-3">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-luxor-surface2 text-luxor-muted"><i class="bi bi-calendar3"></i></span>
                    <div>
                        <small class="block text-xs text-luxor-muted">Fecha</small>
                        <strong>{{ $venta->created_at->format('d/m/Y H:i') }}</strong>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-luxor-surface2 text-luxor-muted"><i class="bi bi-person"></i></span>
                    <div>
                        <small class="block text-xs text-luxor-muted">Cajero</small>
                        <strong>{{ $venta->usuario->nombre_completo }}</strong>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-luxor-surface2 text-luxor-muted"><i class="bi bi-credit-card"></i></span>
                    <div>
                        <small class="block text-xs text-luxor-muted">Método de pago</small>
                        @if($venta->metodo_pago == 'Efectivo')
                            <span class="mt-1 inline-block rounded-full bg-emerald-500/15 px-3 py-1 text-xs text-emerald-500">Efectivo</span>
                        @elseif($venta->metodo_pago == 'Tarjeta')
                            <span class="mt-1 inline-block rounded-full bg-blue-500/15 px-3 py-1 text-xs text-blue-500">Tarjeta</span>
                        @else
                            <span class="mt-1 inline-block rounded-full bg-purple-500/15 px-3 py-1 text-xs text-purple-500">Transferencia</span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-luxor-surface2 text-luxor-muted"><i class="bi bi-check2-circle"></i></span>
                    <div>
                        <small class="block text-xs text-luxor-muted">Estado</small>
                        @if($venta->estado == 'Completada')
                            <span class="mt-1 inline-block rounded-full bg-emerald-500/15 px-3 py-1 text-xs text-emerald-500">Completada</span>
                        @else
                            <span class="mt-1 inline-block rounded-full bg-red-500/15 px-3 py-1 text-xs text-red-500">{{ $venta->estado }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Resumen tipo ticket --}}
        <div class="rounded-xl border border-dashed p-5" style="border-color: var(--lx-border); background: var(--lx-surface2);">
            <div class="flex items-center justify-between text-sm">
                <span class="text-luxor-muted">Ítems</span>
                <strong>{{ $venta->detalles->sum('cantidad') }}</strong>
            </div>
            <div class="my-3 border-t border-dashed" style="border-color: var(--lx-border);"></div>
            <div class="flex items-center justify-between">
                <span class="font-semibold">Total pagado</span>
                <strong class="text-2xl text-emerald-500">$ {{ number_format($venta->total_venta, 0) }}</strong>
            </div>
        </div>
    </div>
</div>
@endsection