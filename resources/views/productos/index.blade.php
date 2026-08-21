@extends('layouts.app')

@section('title', 'Productos')

@section('content')
@php $esAdmin = Auth::user()->rol->nombre_rol == 'Administrador'; @endphp

<div class="mb-2 text-xs text-luxor-muted">Gestión / Productos</div>
<h1 class="mb-2 text-4xl font-semibold tracking-tight">Productos</h1>
<p class="mb-8 text-sm text-luxor-muted">Administra y consulta la información de productos.</p>

<div class="rounded-xl border border-luxor-border bg-luxor-surface shadow-sm">
    {{-- Barra de herramientas --}}
    <div class="flex flex-col gap-3 p-5 sm:flex-row sm:items-center">
        <div class="relative flex-1 sm:max-w-xs">
            <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-sm text-luxor-muted"></i>
            <input id="buscarProducto" type="text" placeholder="Buscar producto, categoría..."
                   class="w-full rounded-full border border-luxor-border bg-luxor-surface2 py-2.5 pl-10 pr-4 text-sm outline-none focus:border-luxor-accent">
        </div>
        <button class="flex items-center gap-2 rounded-full border border-luxor-border bg-luxor-surface px-4 py-2.5 text-sm text-luxor-text">
            Filtrar <i class="bi bi-chevron-down text-xs"></i>
        </button>
        @if($esAdmin)
        <a href="{{ route('productos.create') }}"
           class="sm:ml-auto inline-flex items-center gap-2 rounded-full bg-luxor-accent px-5 py-2.5 text-sm font-semibold text-white hover:bg-luxor-accentDark">
            <i class="bi bi-plus-lg"></i> Nuevo producto
        </a>
        @endif
    </div>

    {{-- Tabla --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-luxor-surface2 text-left text-xs text-luxor-muted">
                    <th class="px-5 py-3 font-semibold">Producto</th>
                    <th class="px-5 py-3 font-semibold">Categoría</th>
                    <th class="px-5 py-3 font-semibold">Stock</th>
                    <th class="px-5 py-3 font-semibold">Precio venta</th>
                    <th class="px-5 py-3 font-semibold">Estado</th>
                    <th class="px-5 py-3 font-semibold">Vencimiento</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody id="tablaProductos">
                @forelse($productos as $p)
                @php
                    $estadoStock = $p->stock_actual <= $p->stock_minimo ? 'Crítico'
                                 : ($p->stock_actual <= $p->stock_minimo * 2 ? 'Bajo' : 'Óptimo');
                    $vence = \Carbon\Carbon::parse($p->fecha_vencimiento);
                    $dias = (int)(($vence->startOfDay()->timestamp - now()->startOfDay()->timestamp) / 86400);
                @endphp
                <tr class="border-t border-luxor-border hover:bg-luxor-surface2/50">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-blue-100 text-blue-600">
                                <i class="bi bi-box-seam"></i>
                            </span>
                            <strong>{{ $p->nombre_producto }}</strong>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-luxor-muted">{{ $p->categoria }}</td>
                    <td class="px-5 py-4"><strong>{{ $p->stock_actual }}</strong> <span class="text-xs text-luxor-muted">un.</span></td>
                    <td class="px-5 py-4 text-luxor-muted">$ {{ number_format($p->precio_venta, 0) }}</td>
                    <td class="px-5 py-4">
                        @if($estadoStock == 'Óptimo')
                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs text-emerald-700">Óptimo</span>
                        @elseif($estadoStock == 'Bajo')
                            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs text-amber-700">Bajo</span>
                        @else
                            <span class="rounded-full bg-red-100 px-3 py-1 text-xs text-red-600">Crítico</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        @if($dias < 0)
                            <span class="text-xs font-semibold text-red-500">Vencido</span>
                        @elseif($dias <= 30)
                            <span class="text-xs font-semibold text-luxor-danger">Vence en {{ $dias }} días</span>
                        @else
                            <span class="text-luxor-muted">{{ $vence->locale('es')->isoFormat('D MMM, YYYY') }}</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-right">
                        @if($esAdmin)
                        <div class="dropdown d-inline-block">
                            <button class="border-0 bg-transparent px-2 text-luxor-muted hover:text-luxor-text" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('productos.edit', $p->id) }}"><i class="bi bi-pencil"></i> Editar</a></li>
                                <li>
                                    <form action="{{ route('productos.destroy', $p->id) }}" method="POST"
                                          onsubmit="return confirm('¿Eliminar {{ $p->nombre_producto }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash"></i> Eliminar</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-luxor-muted">
                        <i class="bi bi-box-seam display-6"></i><br>No hay productos registrados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($productos, 'links'))
    <div class="p-4">{{ $productos->links() }}</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Búsqueda en vivo (filtra las filas de la tabla)
    document.getElementById('buscarProducto').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#tablaProductos tr').forEach(tr => {
            tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>
@endpush