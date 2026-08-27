@extends('layouts.app')

@section('title', 'Historial de Facturas')

@section('content')
<div class="mb-2 text-xs text-luxor-muted">Gestión / Pedidos / Historial de Facturas</div>
<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-4xl font-semibold tracking-tight">Historial de Facturas</h1>
        <p class="mt-2 text-sm text-luxor-muted">Comprobantes de recepción organizados por proveedor.</p>
    </div>
    <a href="{{ route('pedidos.index') }}" class="text-sm text-luxor-muted hover:text-luxor-text">← Volver a pedidos</a>
</div>

{{-- Filtros --}}
<div class="mb-5 rounded-xl border border-luxor-border bg-luxor-surface p-5 shadow-sm">
    <div class="flex flex-wrap items-center gap-4">
        <div class="relative flex-1 sm:max-w-xs">
            <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-sm text-luxor-muted"></i>
            <input id="buscarFactura" type="text" placeholder="Buscar proveedor o pedido..."
                   class="w-full rounded-full border border-luxor-border bg-luxor-surface2 py-2.5 pl-10 pr-4 text-sm outline-none focus:border-luxor-accent">
        </div>
        <select id="filtroProveedor"
                class="rounded-full border border-luxor-border bg-luxor-surface2 px-4 py-2.5 text-sm outline-none focus:border-luxor-accent">
            <option value="">Todos los proveedores</option>
            @foreach($proveedores as $nombre)
            <option value="{{ $nombre }}">{{ $nombre }}</option>
            @endforeach
        </select>
        <span class="text-xs text-luxor-muted"><i class="bi bi-receipt"></i> {{ $facturas->count() }} factura(s) recibida(s)</span>
    </div>
</div>

{{-- Galería de facturas --}}
<div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
    @forelse($facturas as $f)
    <div class="factura-card overflow-hidden rounded-xl border border-luxor-border bg-luxor-surface shadow-sm"
         data-proveedor="{{ $f->proveedor->nombre }}"
         data-texto="{{ strtolower($f->proveedor->nombre . ' pedido #' . $f->id) }}">
        <a href="{{ asset('storage/' . $f->ruta_factura) }}" target="_blank" class="group block overflow-hidden bg-luxor-surface2">
            <img src="{{ asset('storage/' . $f->ruta_factura) }}" alt="Factura pedido #{{ $f->id }}"
                 class="h-56 w-full object-cover transition duration-300 group-hover:scale-105">
        </a>
        <div class="space-y-2 p-4">
            <div class="flex items-center justify-between gap-2">
                <strong class="truncate"><i class="bi bi-truck text-luxor-muted"></i> {{ $f->proveedor->nombre }}</strong>
                @if($f->estado == 'Recibido')
                    <span class="rounded-full bg-emerald-500/15 px-2.5 py-0.5 text-xs text-emerald-500">Recibido</span>
                @elseif($f->estado == 'Parcial')
                    <span class="rounded-full bg-blue-500/15 px-2.5 py-0.5 text-xs text-blue-500">Parcial</span>
                @else
                    <span class="rounded-full bg-amber-500/15 px-2.5 py-0.5 text-xs text-amber-500">{{ $f->estado }}</span>
                @endif
            </div>
            <p class="text-xs text-luxor-muted">
                Pedido #{{ $f->id }} · {{ optional($f->fecha_recepcion)->format('d/m/Y H:i') }}
            </p>
            <p class="text-sm font-semibold text-emerald-500">$ {{ number_format($f->total_pedido, 0) }}</p>
            <div class="flex gap-2 pt-1">
                <a href="{{ route('pedidos.show', $f->id) }}"
                   class="flex-1 rounded-lg bg-luxor-surface2 px-3 py-2 text-center text-xs font-semibold text-luxor-muted transition hover:bg-luxor-accent hover:text-white">
                    <i class="bi bi-eye"></i> Ver pedido
                </a>
                <a href="{{ asset('storage/' . $f->ruta_factura) }}" target="_blank"
                   class="flex-1 rounded-lg bg-luxor-surface2 px-3 py-2 text-center text-xs font-semibold text-luxor-muted transition hover:bg-luxor-accent hover:text-white">
                    <i class="bi bi-box-arrow-up-right"></i> Abrir foto
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full rounded-xl border border-dashed p-12 text-center text-luxor-muted" style="border-color: var(--lx-border);">
        <i class="bi bi-receipt display-6"></i>
        <p class="mt-2">Aún no hay facturas recibidas.</p>
        <small>Cuando recibas un pedido con foto de factura, aparecerá aquí automáticamente.</small>
    </div>
    @endforelse
</div>
@endsection

@push('scripts')
<script>
    function filtrarFacturas() {
        const q = document.getElementById('buscarFactura').value.toLowerCase();
        const prov = document.getElementById('filtroProveedor').value.toLowerCase();
        document.querySelectorAll('.factura-card').forEach(card => {
            const okProv = !prov || card.dataset.proveedor.toLowerCase() === prov;
            const okTexto = !q || card.dataset.texto.includes(q);
            card.style.display = (okProv && okTexto) ? '' : 'none';
        });
    }
    document.getElementById('buscarFactura').addEventListener('input', filtrarFacturas);
    document.getElementById('filtroProveedor').addEventListener('change', filtrarFacturas);
</script>
@endpush