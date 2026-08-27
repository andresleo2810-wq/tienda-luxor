@extends('layouts.app')

@section('title', 'Verificación de Pedido')

@section('content')
<div class="mb-2 text-xs text-luxor-muted">Gestión / Pedidos / #{{ $pedido->id }} / Verificar</div>
<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-4xl font-semibold tracking-tight">Verificación #{{ $pedido->id }}</h1>
        <p class="mt-2 text-sm text-luxor-muted">Ajusta las cantidades que <strong>realmente llegaron</strong> antes de actualizar el inventario.</p>
    </div>
    <a href="{{ route('pedidos.show', $pedido->id) }}" class="text-sm text-luxor-muted hover:text-luxor-text">← Volver</a>
</div>

<div class="mb-5 rounded-lg border border-blue-300 bg-blue-50 px-4 py-3 text-sm text-blue-800">
    <i class="bi bi-cpu"></i> La IA detectó los productos marcados en verde. Lo que confirmes aquí es lo que entra al inventario; si hay diferencias, el pedido quedará como <strong>Parcial</strong>.
</div>

<form action="{{ route('pedidos.confirmar', $pedido->id) }}" method="POST" class="rounded-xl border border-luxor-border bg-luxor-surface shadow-sm">
    @csrf
    @method('PUT')

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-luxor-surface2 text-left text-xs text-luxor-muted">
                    <th class="px-5 py-3 font-semibold">Producto</th>
                    <th class="px-5 py-3 font-semibold">Pedido</th>
                    <th class="px-5 py-3 font-semibold">Análisis IA</th>
                    <th class="px-5 py-3 font-semibold">Recibido real</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pedido->detalles as $i => $detalle)
                <tr class="border-t border-luxor-border">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-blue-500/15 text-blue-500"><i class="bi bi-box-seam"></i></span>
                            <strong>{{ $detalle->producto->nombre_producto }}</strong>
                        </div>
                    </td>
                    <td class="px-5 py-4"><strong>{{ $detalle->cantidad }}</strong> <span class="text-xs text-luxor-muted">un.</span></td>
                    <td class="px-5 py-4">
                        @if($analisis && ($analisis[$i]['detectado'] ?? false))
                            <span class="rounded-full bg-emerald-500/15 px-3 py-1 text-xs text-emerald-500">✅ Detectado</span>
                        @else
                            <span class="rounded-full bg-amber-500/15 px-3 py-1 text-xs text-amber-500">⚠️ No detectado</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <input type="number" name="recibido[{{ $detalle->id }}]" min="0" max="{{ $detalle->cantidad }}"
                               value="{{ $detalle->cantidad }}"
                               class="w-24 rounded-lg border border-luxor-border bg-luxor-surface2 px-3 py-2 text-sm font-bold outline-none focus:border-luxor-accent">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex justify-end gap-2 border-t p-5" style="border-color: var(--lx-border);">
        <a href="{{ route('pedidos.show', $pedido->id) }}"
           class="rounded-full border border-luxor-border bg-luxor-surface2 px-5 py-2.5 text-sm font-semibold text-luxor-muted hover:text-luxor-text">
            Cancelar
        </a>
        <button type="submit" onclick="return confirm('¿Confirmar recepción y actualizar inventario?')"
                class="btn-luxor inline-flex items-center gap-2 rounded-full bg-luxor-accent px-6 py-2.5 text-sm font-semibold text-white hover:bg-luxor-accentDark">
            <i class="bi bi-check2-circle"></i> Confirmar y actualizar inventario
        </button>
    </div>
    @if(!empty($nuevos))
    <div class="mt-5 rounded-xl border border-luxor-border bg-luxor-surface p-5 shadow-sm">
        <h2 class="mb-1 font-semibold"><i class="bi bi-stars text-luxor-accent"></i> 🆕 Productos que la IA detectó y NO existen</h2>
        <p class="mb-4 text-xs text-luxor-muted">Marca los que quieras crear automáticamente. El precio de venta sugerido es costo × 1.4 (puedes editarlo).</p>
        <div class="space-y-3">
            @foreach($nuevos as $i => $nuevo)
            <div class="flex flex-wrap items-center gap-3 rounded-lg border px-3 py-2" style="border-color: var(--lx-border); background: var(--lx-surface2);">
                <input type="checkbox" name="nuevos[{{ $i }}][crear]" value="1" checked>
                                <label class="text-xs text-luxor-muted">Cant.
                    <input type="number" name="nuevos[{{ $i }}][cantidad]" value="{{ $nuevo['cantidad'] ?? 1 }}" min="1"
                           class="w-16 rounded-lg border px-2 py-1.5 text-sm" style="border-color: var(--lx-border); background: var(--lx-surface); color: var(--lx-text);">
                </label>
                @if($nuevo['volumen'] ?? '')
                    <span class="text-xs text-purple-600 bg-purple-500/15 px-2 py-1 rounded">{{ $nuevo['volumen'] }}</span>
                @endif
                @if($nuevo['grado'] ?? '')
                    <span class="text-xs text-orange-600 bg-orange-500/15 px-2 py-1 rounded">{{ $nuevo['grado'] }}</span>
                @endif
                <input type="text" name="nuevos[{{ $i }}][nombre]" value="{{ $nuevo['nombre'] }}"
                       class="min-w-0 flex-1 rounded-lg border px-3 py-1.5 text-sm" style="border-color: var(--lx-border); background: var(--lx-surface); color: var(--lx-text);">
                <label class="text-xs text-luxor-muted">Costo $
                    <input type="number" name="nuevos[{{ $i }}][costo]" value="{{ $nuevo['precio'] }}"
                           class="w-24 rounded-lg border px-2 py-1.5 text-sm" style="border-color: var(--lx-border); background: var(--lx-surface); color: var(--lx-text);">
                </label>
                <label class="text-xs text-luxor-muted">Venta $
                    <input type="number" name="nuevos[{{ $i }}][venta]" value="{{ round($nuevo['precio'] * 1.4) }}"
                           class="w-24 rounded-lg border px-2 py-1.5 text-sm" style="border-color: var(--lx-border); background: var(--lx-surface); color: var(--lx-text);">
                </label>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</form>
@endsection