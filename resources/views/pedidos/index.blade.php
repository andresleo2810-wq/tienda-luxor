@extends('layouts.app')

@section('title', 'Pedidos')

@section('content')
<div class="mb-2 text-xs text-luxor-muted">Gestión / Pedidos</div>
<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-4xl font-semibold tracking-tight">Pedidos</h1>
        <p class="mt-2 text-sm text-luxor-muted">Gestiona las compras a proveedores y su recepción con IA.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('pedidos.facturas') }}"
           class="btn-luxor inline-flex items-center gap-2 rounded-full border border-luxor-border bg-luxor-surface px-5 py-2.5 text-sm font-semibold text-luxor-muted hover:text-luxor-text">
            <i class="bi bi-receipt"></i> Historial
        </a>
        <button type="button" data-bs-toggle="modal" data-bs-target="#modalVozPedido"
                class="btn-luxor inline-flex items-center gap-2 rounded-full bg-luxor-danger px-5 py-2.5 text-sm font-semibold text-white hover:opacity-90">
            <i class="bi bi-mic-fill"></i> Por Voz
        </button>
        <a href="{{ route('pedidos.create') }}"
           class="btn-luxor inline-flex items-center gap-2 rounded-full bg-luxor-accent px-5 py-2.5 text-sm font-semibold text-white hover:bg-luxor-accentDark">
            <i class="bi bi-plus-lg"></i> Nuevo Pedido
        </a>
    </div>
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

{{-- ============ MODAL PEDIDO POR VOZ ============ --}}
<div class="modal fade" id="modalVozPedido" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-2xl" style="background: var(--lx-surface); color: var(--lx-text); border-radius: 1.25rem;">
            <div class="modal-header border-0 px-5 pb-0 pt-5">
                <h5 class="modal-title flex items-center gap-3 font-semibold">
                    <span class="grid h-10 w-10 place-items-center rounded-full bg-luxor-danger text-white shadow-md"><i class="bi bi-mic-fill"></i></span>
                    Pedido por Voz
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="resetCarritoVoz()"></button>
            </div>
            <div class="modal-body px-5 pb-5">

                {{-- Proveedor --}}
                <div class="mb-4">
                    <label class="mb-1 block text-xs font-semibold text-luxor-muted">Proveedor del pedido</label>
                    <select name="id_proveedor" id="voz-pedido-proveedor"
                            class="w-full rounded-lg border px-3 py-2 text-sm outline-none focus:border-luxor-accent"
                            style="border-color: var(--lx-border); background: var(--lx-surface2);">
                    </select>
                </div>

                {{-- Área de dictado --}}
                <div class="rounded-xl border p-4" style="border-color: var(--lx-border); background: var(--lx-surface2);">
                    <div class="mb-2 flex items-center justify-between">
                        <small class="text-xs text-luxor-muted">🎤 Dicta <strong>un producto a la vez</strong> y lo agrego al carrito</small>
                        <span id="contador-carrito-voz" class="rounded-full bg-luxor-accent px-2.5 py-0.5 text-xs font-bold text-white">0 items</span>
                    </div>

                    <div class="flex items-center gap-3">
                        <button id="btn-mic-pedido" type="button"
                                class="btn-luxor grid h-16 w-16 shrink-0 place-items-center rounded-full bg-luxor-danger text-white shadow-lg transition hover:scale-105">
                            <i class="bi bi-mic-fill fs-3"></i>
                        </button>
                        <div class="flex-1">
                            <p id="estado-mic-pedido" class="text-sm font-semibold">Presiona el micrófono y dicta</p>
                            <p class="text-xs text-luxor-muted">Ej: "10 whisky black label"</p>
                        </div>
                    </div>

                    {{-- Input manual --}}
                    <div class="mt-3 flex gap-2">
                        <input type="text" id="texto-pedido"
                               class="w-full rounded-lg border px-3 py-2 text-sm outline-none focus:border-luxor-accent"
                               style="border-color: var(--lx-border); background: var(--lx-surface);"
                               placeholder="O escribe aquí: 5 cervezas aguila">
                        <button type="button"
                                class="btn-luxor whitespace-nowrap rounded-lg bg-luxor-accent px-4 py-2 text-sm font-semibold text-white hover:bg-luxor-accentDark"
                                onclick="dictarProductoVoz(document.getElementById('texto-pedido').value)">
                            <i class="bi bi-plus-lg"></i> Agregar
                        </button>
                    </div>
                </div>

                {{-- Carrito de productos dictados --}}
                <div class="mt-4">
                    <h6 class="mb-2 text-sm font-semibold"><i class="bi bi-cart3 text-luxor-accent"></i> Productos del pedido</h6>
                    <div id="carrito-voz-vacio" class="rounded-lg border border-dashed py-6 text-center text-sm text-luxor-muted" style="border-color: var(--lx-border);">
                        Aún no has dictado productos
                    </div>
                    <div id="carrito-voz-lista" class="space-y-2"></div>
                </div>

                {{-- Última interpretación --}}
                <div id="ultima-interpretacion" class="mt-3 rounded-lg px-3 py-2 text-xs" style="background: var(--lx-surface2); display: none;">
                    <strong>🎧 Última dictado:</strong> "<span id="pedido-escuchado"></span>" → <span id="pedido-interpretacion"></span>
                </div>

                {{-- Botones finales --}}
                <div class="mt-4 flex gap-2">
                    <button type="button" id="btn-crear-pedido-voz" onclick="crearPedidoVoz()" disabled
                            class="btn-luxor w-full rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-40">
                        <i class="bi bi-check2-circle"></i> Crear Pedido (<span id="btn-contador">0</span>)
                    </button>
                    <button type="button" onclick="resetCarritoVoz()"
                            class="w-full rounded-lg border px-4 py-2.5 text-sm font-semibold text-luxor-muted" style="border-color: var(--lx-border);">
                        <i class="bi bi-trash"></i> Vaciar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="form-pedido-voz" action="{{ route('pedidos.voz') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="id_proveedor" id="form-voz-proveedor">
    <div id="form-voz-items"></div>
</form>
@endsection

@push('scripts')
@php
    $productosVoz = \App\Models\Producto::where('estado', true)->get(['id', 'nombre_producto', 'precio_costo']);
    $proveedoresVoz = \App\Models\Proveedor::where('estado', true)->get(['id', 'nombre']);
@endphp
<script>
    const PRODUCTOS_VOZ = @json($productosVoz);
    const PROVEEDORES_VOZ = @json($proveedoresVoz);
    let carritoVoz = [];

    // Cargar proveedores en select
    document.addEventListener('DOMContentLoaded', () => {
        const sel = document.getElementById('voz-pedido-proveedor');
        sel.innerHTML = PROVEEDORES_VOZ.map(p => '<option value="' + p.id + '">' + p.nombre + '</option>').join('');
    });

    function normVoz(t) { return t.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, ''); }

    // Micrófono
    document.getElementById('btn-mic-pedido').addEventListener('click', function () {
        const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SR) { alert('Usa Google Chrome para el reconocimiento de voz.'); return; }
        const rec = new SR();
        rec.lang = 'es-CO';
                rec.onstart = () => document.getElementById('estado-mic-pedido').textContent = '🎙️ Escuchando...';
        rec.onresult = e => dictarProductoVoz(e.results[0][0].transcript);
        rec.onerror = e => {
            const el = document.getElementById('estado-mic-pedido');
            if (e.error === 'aborted' || e.error === 'no-speech') {
                el.textContent = '🎙️ No te escuché bien. Toca el micrófono y habla de nuevo.';
            } else if (e.error === 'not-allowed' || e.error === 'service-not-allowed') {
                el.textContent = '🔒 Permiso de micrófono denegado. Actívalo en los ajustes del navegador.';
            } else if (e.error === 'network') {
                el.textContent = '📶 El servicio de voz necesita internet. Revisa tu conexión.';
            } else {
                el.textContent = '❌ Error: ' + e.error;
            }
        };
        rec.onend = () => document.getElementById('estado-mic-pedido').textContent = 'Presiona el micrófono y dicta';
        rec.start();
    });

    function dictarProductoVoz(texto) {
        if (!texto) return;
        const cmd = normVoz(texto);

        // Cantidad
        const mCantidad = cmd.match(/(\d+)/);
        const cantidad = mCantidad ? parseInt(mCantidad[1]) : 1;

        // Stop words
        const stop = ['pedido','pedir','de','a','y','un','una','botellas','botella','unidades','unidad','cajas','haz','por','favor','mas','más'];
        const query = cmd.replace(/\d+/g, ' ').split(/\s+/).filter(w => w.length > 2 && !stop.includes(w));

        // Buscar producto
        let mejor = null, mejorScore = 0;
        for (const p of PRODUCTOS_VOZ) {
            const nombre = normVoz(p.nombre_producto).split(/\s+/);
            let score = 0;
            query.forEach(q => { if (nombre.some(n => n.includes(q) || q.includes(n))) score++; });
            if (score > mejorScore) { mejorScore = score; mejor = p; }
        }

        const interpretacionEl = document.getElementById('ultima-interpretacion');
        const escuchadoEl = document.getElementById('pedido-escuchado');
        const interpretadoEl = document.getElementById('pedido-interpretacion');

        if (!mejor) {
            escuchadoEl.textContent = texto;
            interpretadoEl.innerHTML = '<span class="text-red-500">❌ No entendí el producto. Intenta de nuevo.</span>';
            interpretacionEl.style.display = 'block';
            return;
        }

        // Agregar al carrito
        const existente = carritoVoz.find(i => i.id === mejor.id);
        if (existente) {
            existente.cantidad += cantidad;
        } else {
            carritoVoz.push({ id: mejor.id, nombre: mejor.nombre_producto, cantidad: cantidad, costo: mejor.precio_costo });
        }

        // Mostrar interpretación
        escuchadoEl.textContent = texto;
        interpretadoEl.innerHTML = '✅ Agregado: <strong>' + cantidad + ' x ' + mejor.nombre_producto + '</strong>';
        interpretacionEl.style.display = 'block';

        // Limpiar input y renderizar
        document.getElementById('texto-pedido').value = '';
        renderCarritoVoz();
    }

    function renderCarritoVoz() {
        const lista = document.getElementById('carrito-voz-lista');
        const vacio = document.getElementById('carrito-voz-vacio');

        if (carritoVoz.length === 0) {
            vacio.style.display = 'block';
            lista.innerHTML = '';
            document.getElementById('btn-crear-pedido-voz').disabled = true;
        } else {
            vacio.style.display = 'none';
            document.getElementById('btn-crear-pedido-voz').disabled = false;
            lista.innerHTML = carritoVoz.map((item, i) => `
                <div class="flex items-center gap-3 rounded-lg border px-3 py-2" style="border-color: var(--lx-border); background: var(--lx-surface2);">
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-blue-500/15 text-blue-500"><i class="bi bi-box-seam"></i></span>
                    <div class="min-w-0 flex-1">
                        <strong class="block truncate text-sm">${item.nombre}</strong>
                        <div class="mt-1 flex items-center gap-2">
                            <button type="button" onclick="cambiarCantVoz(${i}, -1)" class="grid h-6 w-6 place-items-center rounded bg-luxor-surface text-xs hover:bg-luxor-accent hover:text-white">−</button>
                            <strong class="w-8 text-center text-sm">${item.cantidad}</strong>
                            <button type="button" onclick="cambiarCantVoz(${i}, 1)" class="grid h-6 w-6 place-items-center rounded bg-luxor-surface text-xs hover:bg-luxor-accent hover:text-white">+</button>
                            <span class="ml-2 text-xs text-luxor-muted">x $ ${item.costo.toLocaleString('es-CO')}</span>
                        </div>
                    </div>
                    <strong class="text-sm text-emerald-500">$ ${(item.cantidad * item.costo).toLocaleString('es-CO')}</strong>
                    <button type="button" onclick="quitarVoz(${i})" class="text-luxor-muted hover:text-red-500" title="Quitar"><i class="bi bi-trash"></i></button>
                </div>
            `).join('');
        }

        const totalItems = carritoVoz.reduce((a, b) => a + b.cantidad, 0);
        document.getElementById('contador-carrito-voz').textContent = carritoVoz.length + ' item' + (carritoVoz.length !== 1 ? 's' : '');
        document.getElementById('btn-contador').textContent = totalItems;
    }

    function cambiarCantVoz(idx, delta) {
        carritoVoz[idx].cantidad = Math.max(1, carritoVoz[idx].cantidad + delta);
        renderCarritoVoz();
    }

    function quitarVoz(idx) {
        carritoVoz.splice(idx, 1);
        renderCarritoVoz();
    }

    function resetCarritoVoz() {
        carritoVoz = [];
        renderCarritoVoz();
        document.getElementById('ultima-interpretacion').style.display = 'none';
    }

    function crearPedidoVoz() {
        if (carritoVoz.length === 0) return;
        const form = document.getElementById('form-pedido-voz');
        document.getElementById('form-voz-proveedor').value = document.getElementById('voz-pedido-proveedor').value;
        document.getElementById('form-voz-items').innerHTML = carritoVoz.map((item, i) =>
            '<input type="hidden" name="productos[' + i + '][id]" value="' + item.id + '">' +
            '<input type="hidden" name="productos[' + i + '][cantidad]" value="' + item.cantidad + '">'
        ).join('');
        form.submit();
    }

    // Buscador de la tabla
    document.getElementById('buscarPedido').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#tablaPedidos tr').forEach(tr => {
            tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>
@endpush