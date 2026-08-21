@extends('layouts.app')

@section('title', 'Pedidos')

@section('content')
<div class="mb-2 text-xs text-luxor-muted">Gestión / Pedidos</div>
<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-4xl font-semibold tracking-tight">Pedidos</h1>
        <p class="mt-2 text-sm text-luxor-muted">Gestiona las compras a proveedores y su recepción con IA.</p>
    </div>
    <a href="{{ route('pedidos.create') }}"
       class="btn-luxor inline-flex items-center gap-2 rounded-full bg-luxor-accent px-5 py-2.5 text-sm font-semibold text-white hover:bg-luxor-accentDark">
        <i class="bi bi-plus-lg"></i> Nuevo Pedido
    </a>
</div>

<div class="rounded-xl border border-luxor-border bg-luxor-surface shadow-sm">
    <div class="p-5">
        <div class="relative sm:max-w-xs">
            <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-sm text-luxor-muted"></i>
            <input id="buscarPedido" type="text" placeholder="Buscar pedido, proveedor..."
                   class="w-full rounded-full border border-luxor-border bg-luxor-surface2 py-2.5 pl-10 pr-4 text-sm outline-none focus:border-luxor-accent">
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-luxor-surface2 text-left text-xs text-luxor-muted">
                    <th class="px-5 py-3 font-semibold">#</th>
                    <th class="px-5 py-3 font-semibold">Proveedor</th>
                    <th class="px-5 py-3 font-semibold">Fecha</th>
                    <th class="px-5 py-3 font-semibold">Solicitó</th>
                    <th class="px-5 py-3 font-semibold">Total</th>
                    <th class="px-5 py-3 font-semibold">Estado</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody id="tablaPedidos">
                @forelse($pedidos as $pedido)
                <tr class="border-t border-luxor-border hover:bg-luxor-surface2/50">
                    <td class="px-5 py-4"><strong>#{{ $pedido->id }}</strong></td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-purple-500/15 text-purple-500"><i class="bi bi-truck"></i></span>
                            <strong>{{ $pedido->proveedor->nombre }}</strong>
                        </div>
                    </td>
                    <td class="px-5 py-4 whitespace-nowrap text-luxor-muted">{{ $pedido->fecha_pedido->format('d/m/Y') }}</td>
                    <td class="px-5 py-4 text-luxor-muted">{{ $pedido->usuario->nombre_completo }}</td>
                    <td class="px-5 py-4 whitespace-nowrap font-semibold">$ {{ number_format($pedido->total_pedido, 0) }}</td>
                    <td class="px-5 py-4">
                        @if($pedido->estado == 'Pendiente')
                            <span class="rounded-full bg-amber-500/15 px-3 py-1 text-xs text-amber-500">Pendiente</span>
                        @elseif($pedido->estado == 'Recibido')
                            <span class="rounded-full bg-emerald-500/15 px-3 py-1 text-xs text-emerald-500">Recibido</span>
                        @elseif($pedido->estado == 'Parcial')
                            <span class="rounded-full bg-blue-500/15 px-3 py-1 text-xs text-blue-500">Parcial</span>
                        @else
                            <span class="rounded-full bg-red-500/15 px-3 py-1 text-xs text-red-500">Cancelado</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-right">
                        <a href="{{ route('pedidos.show', $pedido->id) }}" title="Ver detalle"
                           class="inline-grid h-8 w-8 place-items-center rounded-lg bg-luxor-surface2 text-luxor-muted transition hover:bg-luxor-accent hover:text-white">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-luxor-muted">
                        <i class="bi bi-box-arrow-in-down display-6"></i><br>No hay pedidos registrados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pedidos->hasPages())
    <div class="p-4">{{ $pedidos->links() }}</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('buscarPedido').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#tablaPedidos tr').forEach(tr => {
            tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>
@endpush