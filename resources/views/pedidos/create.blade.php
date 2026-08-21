@extends('layouts.app')

@section('title', 'Nuevo Pedido')

@section('content')
<div class="mb-2 text-xs text-luxor-muted">Gestión / Pedidos / Nuevo</div>
<div class="mb-8 flex items-end justify-between">
    <div>
        <h1 class="text-4xl font-semibold tracking-tight">Nuevo Pedido</h1>
        <p class="mt-2 text-sm text-luxor-muted">Registra una compra a proveedor.</p>
    </div>
    <a href="{{ route('pedidos.index') }}" class="text-sm text-luxor-muted hover:text-luxor-text">← Volver</a>
</div>

<form id="formPedido" action="{{ route('pedidos.store') }}" method="POST">
    @csrf
    <div class="grid gap-5 xl:grid-cols-[1.5fr_1fr]">
        <div class="space-y-5">
            {{-- Datos del proveedor --}}
            <div class="rounded-xl border border-luxor-border bg-luxor-surface p-5 shadow-sm">
                <h2 class="mb-4 font-semibold"><i class="bi bi-truck text-luxor-accent"></i> Datos del pedido</h2>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium">Proveedor *</label>
                        <select name="id_proveedor" required
                                class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 px-4 py-3 text-sm outline-none focus:border-luxor-accent">
                            <option value="">Seleccione...</option>
                            @foreach($proveedores as $prov)
                            <option value="{{ $prov->id }}" {{ old('id_proveedor') == $prov->id ? 'selected' : '' }}>{{ $prov->nombre }}</option>
                            @endforeach
                        </select>
                        @error('id_proveedor') <p class="mt-1 text-xs text-luxor-danger">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium">Fecha del pedido *</label>
                        <input type="date" name="fecha_pedido" required value="{{ old('fecha_pedido', now()->format('Y-m-d')) }}"
                               class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 px-4 py-3 text-sm outline-none focus:border-luxor-accent">
                        @error('fecha_pedido') <p class="mt-1 text-xs text-luxor-danger">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-2 block text-sm font-medium">Observaciones</label>
                        <textarea name="observaciones" rows="2"
                                  class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 px-4 py-3 text-sm outline-none focus:border-luxor-accent"
                                  placeholder="Notas para el proveedor...">{{ old('observaciones') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Selector de productos --}}
            <div class="rounded-xl border border-luxor-border bg-luxor-surface shadow-sm">
                <div class="border-b p-5" style="border-color: var(--lx-border);">
                    <div class="relative">
                        <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-sm text-luxor-muted"></i>
                        <input id="buscarProducto" type="text" placeholder="Buscar producto..."
                               class="w-full rounded-full border border-luxor-border bg-luxor-surface2 py-2.5 pl-10 pr-4 text-sm outline-none focus:border-luxor-accent">
                    </div>
                </div>
                <div id="listaProductos" class="max-h-[400px] divide-y overflow-y-auto"></div>
            </div>
        </div>

        {{-- Carrito --}}
        <div class="h-fit rounded-xl border border-luxor-border bg-luxor-surface shadow-sm">
            <div class="flex items-center justify-between border-b p-5" style="border-color: var(--lx-border);">
                <h2 class="font-semibold"><i class="bi bi-cart3 text-luxor-accent"></i> Productos del pedido</h2>
                <span id="contadorItems" class="rounded-full bg-luxor-accent px-2.5 py-0.5 text-xs font-bold text-white">0</span>
            </div>

            <div id="carritoVacio" class="p-8 text-center text-sm text-luxor-muted">
                <i class="bi bi-box-seam display-6"></i>
                <p class="mt-2">Agrega productos al pedido</p>
            </div>

            <div id="carritoLista" class="divide-y"></div>
            <div id="carritoInputs"></div>

            <div class="space-y-4 p-5">
                <div class="flex items-center justify-between">
                    <span class="text-luxor-muted">Total pedido</span>
                    <strong id="totalPedido" class="text-2xl text-emerald-500">$ 0</strong>
                </div>
                <button type="submit" id="btnConfirmar" disabled
                        class="btn-luxor w-full rounded-lg bg-luxor-accent px-4 py-3 text-sm font-semibold text-white transition hover:bg-luxor-accentDark disabled:cursor-not-allowed disabled:opacity-40">
                    <i class="bi bi-check2-circle"></i> Crear Pedido
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    const PRODUCTOS = @json($productos);
    let carrito = [];

    function fmt(n) { return '$ ' + new Intl.NumberFormat('es-CO').format(n); }

    function renderProductos(filtro = '') {
        const f = filtro.toLowerCase();
        const lista = PRODUCTOS.filter(p => p.nombre_producto.toLowerCase().includes(f));
        document.getElementById('listaProductos').innerHTML = lista.map(p => `
            <div class="flex items-center gap-3 px-5 py-3">
                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-blue-500/15 text-blue-500"><i class="bi bi-box-seam"></i></span>
                <div class="min-w-0 flex-1">
                    <strong class="block truncate text-sm">${p.nombre_producto}</strong>
                    <small class="text-xs text-luxor-muted">Costo ref: ${fmt(p.precio_costo)}</small>
                </div>
                <button type="button" onclick="agregar(${p.id})"
                        class="btn-luxor grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-luxor-accent text-white hover:bg-luxor-accentDark">
                    <i class="bi bi-plus-lg"></i>
                </button>
            </div>
        `).join('') || '<p class="p-6 text-center text-sm text-luxor-muted">Sin resultados</p>';
    }

    function agregar(id) {
        const p = PRODUCTOS.find(x => x.id === id);
        if (carrito.find(x => x.id === id)) return;
        carrito.push({ id, cantidad: 1, costo: parseFloat(p.precio_costo) || 0 });
        renderCarrito();
    }

    function cambiar(id, delta) {
        const item = carrito.find(x => x.id === id);
        if (!item) return;
        item.cantidad = Math.max(1, item.cantidad + delta);
        renderCarrito();
    }

    function setCosto(id, valor) {
        const item = carrito.find(x => x.id === id);
        if (!item) return;
        item.costo = parseFloat(valor) || 0;
        renderCarrito(false);
    }

    function quitar(id) {
        carrito = carrito.filter(x => x.id !== id);
        renderCarrito();
    }

    function renderCarrito(redibujar = true) {
        const lista = document.getElementById('carritoLista');
        let total = 0;

        if (redibujar) {
            lista.innerHTML = carrito.map(item => {
                const p = PRODUCTOS.find(x => x.id === item.id);
                return `
                <div class="px-5 py-3">
                    <div class="flex items-center gap-2">
                        <div class="min-w-0 flex-1">
                            <strong class="block truncate text-sm">${p.nombre_producto}</strong>
                        </div>
                        <button type="button" onclick="quitar(${p.id})" class="text-luxor-muted hover:text-luxor-danger"><i class="bi bi-trash"></i></button>
                    </div>
                    <div class="mt-2 flex items-center gap-2 text-xs">
                        <div class="flex items-center gap-1">
                            <button type="button" onclick="cambiar(${p.id}, -1)" class="grid h-7 w-7 place-items-center rounded-md bg-luxor-surface2 text-luxor-muted">−</button>
                            <span class="w-8 text-center font-bold">${item.cantidad}</span>
                            <button type="button" onclick="cambiar(${p.id}, 1)" class="grid h-7 w-7 place-items-center rounded-md bg-luxor-surface2 text-luxor-muted">+</button>
                        </div>
                        <span class="text-luxor-muted">x</span>
                        <input type="number" step="any" min="0" value="${item.costo}" onchange="setCosto(${p.id}, this.value)"
                               class="w-24 rounded-md border border-luxor-border bg-luxor-surface2 px-2 py-1 text-xs outline-none focus:border-luxor-accent">
                        <span class="ml-auto font-semibold">${fmt(item.cantidad * item.costo)}</span>
                    </div>
                </div>`;
            }).join('');
        }

        total = carrito.reduce((a, b) => a + b.cantidad * b.costo, 0);

        document.getElementById('carritoInputs').innerHTML = carrito.map((item, i) => `
            <input type="hidden" name="productos[${i}][id]" value="${item.id}">
            <input type="hidden" name="productos[${i}][cantidad]" value="${item.cantidad}">
            <input type="hidden" name="productos[${i}][costo]" value="${item.costo}">
        `).join('');

        document.getElementById('carritoVacio').style.display = carrito.length ? 'none' : 'block';
        document.getElementById('contadorItems').textContent = carrito.length;
        document.getElementById('totalPedido').textContent = fmt(total);
        document.getElementById('btnConfirmar').disabled = carrito.length === 0;
    }

    document.getElementById('buscarProducto').addEventListener('input', function () {
        renderProductos(this.value);
    });

    renderProductos();
    renderCarrito();
</script>
@endpush