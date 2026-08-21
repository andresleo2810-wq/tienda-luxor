@extends('layouts.app')

@section('title', 'Nueva Venta')

@section('content')
<div class="mb-2 text-xs text-luxor-muted">Gestión / Ventas / Nueva</div>
<div class="mb-8 flex items-end justify-between">
    <div>
        <h1 class="text-4xl font-semibold tracking-tight">Nueva Venta</h1>
        <p class="mt-2 text-sm text-luxor-muted">Selecciona productos y confirma el pago.</p>
    </div>
    <a href="{{ route('ventas.index') }}" class="text-sm text-luxor-muted hover:text-luxor-text">← Volver</a>
</div>

<form id="formVenta" action="{{ route('ventas.store') }}" method="POST">
    @csrf
    <input type="hidden" name="metodo_pago" id="metodoPagoInput" value="Efectivo">

    <div class="grid gap-5 xl:grid-cols-[1.5fr_1fr]">
        {{-- ============ SELECTOR DE PRODUCTOS ============ --}}
        <div class="rounded-xl border border-luxor-border bg-luxor-surface shadow-sm">
            <div class="border-b p-5" style="border-color: var(--lx-border);">
                <div class="relative">
                    <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-sm text-luxor-muted"></i>
                    <input id="buscarProducto" type="text" placeholder="Buscar producto..."
                           class="w-full rounded-full border border-luxor-border bg-luxor-surface2 py-2.5 pl-10 pr-4 text-sm outline-none focus:border-luxor-accent">
                </div>
            </div>
            <div id="listaProductos" class="max-h-[480px] divide-y overflow-y-auto" style="border-color: var(--lx-border);"></div>
        </div>

        {{-- ============ CARRITO ============ --}}
        <div class="h-fit rounded-xl border border-luxor-border bg-luxor-surface shadow-sm">
            <div class="flex items-center justify-between border-b p-5" style="border-color: var(--lx-border);">
                <h2 class="font-semibold"><i class="bi bi-cart3 text-luxor-accent"></i> Carrito</h2>
                <span id="contadorItems" class="rounded-full bg-luxor-accent px-2.5 py-0.5 text-xs font-bold text-white">0</span>
            </div>

            <div id="carritoVacio" class="p-8 text-center text-sm text-luxor-muted">
                <i class="bi bi-cart-plus display-6"></i>
                <p class="mt-2">Agrega productos para comenzar</p>
            </div>

            <div id="carritoLista" class="divide-y" style="border-color: var(--lx-border);"></div>
            <div id="carritoInputs"></div>

            <div class="space-y-4 p-5">
                <div class="flex items-center justify-between text-lg">
                    <span class="text-luxor-muted">Total</span>
                    <strong id="totalVenta" class="text-2xl text-emerald-500">$ 0</strong>
                </div>

                {{-- Método de pago --}}
                <div>
                    <p class="mb-2 text-xs font-semibold text-luxor-muted">MÉTODO DE PAGO</p>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" onclick="setMetodo('Efectivo', this)"
                                class="metodo-btn rounded-lg border px-3 py-2.5 text-xs font-semibold"
                                style="border-color: var(--lx-accent); background: var(--lx-accent); color:#fff;">
                            <i class="bi bi-cash-stack"></i> Efectivo
                        </button>
                        <button type="button" onclick="setMetodo('Tarjeta', this)"
                                class="metodo-btn rounded-lg border border-luxor-border bg-luxor-surface2 px-3 py-2.5 text-xs font-semibold text-luxor-muted">
                            <i class="bi bi-credit-card"></i> Tarjeta
                        </button>
                        <button type="button" onclick="setMetodo('Transferencia', this)"
                                class="metodo-btn rounded-lg border border-luxor-border bg-luxor-surface2 px-3 py-2.5 text-xs font-semibold text-luxor-muted">
                            <i class="bi bi-phone"></i> Transfer.
                        </button>
                    </div>
                </div>

                <button type="submit" id="btnConfirmar" disabled
                        class="btn-luxor w-full rounded-lg bg-luxor-accent px-4 py-3 text-sm font-semibold text-white transition hover:bg-luxor-accentDark disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:transform-none">
                    <i class="bi bi-check2-circle"></i> Confirmar Venta
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
        const lista = PRODUCTOS.filter(p => p.nombre_producto.toLowerCase().includes(f) || (p.categoria || '').toLowerCase().includes(f));
        document.getElementById('listaProductos').innerHTML = lista.map(p => `
            <div class="flex items-center gap-3 px-5 py-3">
                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-blue-500/15 text-blue-500"><i class="bi bi-box-seam"></i></span>
                <div class="min-w-0 flex-1">
                    <strong class="block truncate text-sm">${p.nombre_producto}</strong>
                    <small class="text-xs text-luxor-muted">${p.categoria || ''} · Stock: ${p.stock_actual} un.</small>
                </div>
                <span class="text-sm text-luxor-muted">${fmt(p.precio_venta)}</span>
                        <button type="button" onclick="agregar(${p.id})"
                        class="btn-luxor grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-luxor-accent text-white hover:bg-luxor-accentDark">
                    <i class="bi bi-plus-lg"></i>
                </button>
            </div>
        `).join('') || '<p class="p-6 text-center text-sm text-luxor-muted">Sin resultados</p>';
    }

    function agregar(id) {
        const p = PRODUCTOS.find(x => x.id === id);
        const item = carrito.find(x => x.id === id);
        const enCarrito = item ? item.cantidad : 0;
        if (enCarrito + 1 > p.stock_actual) { alert('Stock insuficiente de ' + p.nombre_producto); return; }
        if (item) item.cantidad++; else carrito.push({ id, cantidad: 1 });
        renderCarrito();
    }

    function cambiar(id, delta) {
        const p = PRODUCTOS.find(x => x.id === id);
        const item = carrito.find(x => x.id === id);
        if (!item) return;
        const nueva = item.cantidad + delta;
        if (nueva < 1) return;
        if (nueva > p.stock_actual) { alert('Solo hay ' + p.stock_actual + ' en stock'); return; }
        item.cantidad = nueva;
        renderCarrito();
    }

    function quitar(id) {
        carrito = carrito.filter(x => x.id !== id);
        renderCarrito();
    }

    function renderCarrito() {
        const lista = document.getElementById('carritoLista');
        const inputs = document.getElementById('carritoInputs');
        let total = 0;

        lista.innerHTML = carrito.map((item, i) => {
            const p = PRODUCTOS.find(x => x.id === item.id);
            const sub = p.precio_venta * item.cantidad;
            total += sub;
            return `
            <div class="flex items-center gap-3 px-5 py-3">
                <div class="min-w-0 flex-1">
                    <strong class="block truncate text-sm">${p.nombre_producto}</strong>
                    <small class="text-xs text-luxor-muted">${fmt(p.precio_venta)} c/u</small>
                </div>
                <div class="flex items-center gap-1">
                    <button type="button" onclick="cambiar(${p.id}, -1)" class="grid h-7 w-7 place-items-center rounded-md bg-luxor-surface2 text-luxor-muted">−</button>
                    <span class="w-8 text-center text-sm font-bold">${item.cantidad}</span>
                    <button type="button" onclick="cambiar(${p.id}, 1)" class="grid h-7 w-7 place-items-center rounded-md bg-luxor-surface2 text-luxor-muted">+</button>
                </div>
                <span class="w-24 text-right text-sm font-semibold">${fmt(sub)}</span>
                <button type="button" onclick="quitar(${p.id})" class="text-luxor-muted hover:text-luxor-danger"><i class="bi bi-trash"></i></button>
            </div>`;
        }).join('');

        inputs.innerHTML = carrito.map((item, i) => `
            <input type="hidden" name="productos[${i}][id]" value="${item.id}">
            <input type="hidden" name="productos[${i}][cantidad]" value="${item.cantidad}">
        `).join('');

        document.getElementById('carritoVacio').style.display = carrito.length ? 'none' : 'block';
        document.getElementById('contadorItems').textContent = carrito.reduce((a, b) => a + b.cantidad, 0);
        document.getElementById('totalVenta').textContent = fmt(total);
        document.getElementById('btnConfirmar').disabled = carrito.length === 0;
    }

    function setMetodo(metodo, btn) {
        document.getElementById('metodoPagoInput').value = metodo;
        document.querySelectorAll('.metodo-btn').forEach(b => {
            b.style.background = 'var(--lx-surface2)';
            b.style.borderColor = 'var(--lx-border)';
            b.style.color = 'var(--lx-muted)';
        });
        btn.style.background = 'var(--lx-accent)';
        btn.style.borderColor = 'var(--lx-accent)';
        btn.style.color = '#fff';
    }

    document.getElementById('buscarProducto').addEventListener('input', function () {
        renderProductos(this.value);
    });

    renderProductos();
    renderCarrito();
</script>
@endpush