@extends('layouts.app')

@section('title', 'Ventas')

@section('content')
<div class="mb-2 text-xs text-luxor-muted">Gestión / Ventas</div>
<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-4xl font-semibold tracking-tight">Ventas</h1>
        <p class="mt-2 text-sm text-luxor-muted">Registra y consulta las ventas de tu tienda.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('ventas.create') }}"
           class="btn-luxor inline-flex items-center gap-2 rounded-full bg-luxor-accent px-5 py-2.5 text-sm font-semibold text-white hover:bg-luxor-accentDark">
            <i class="bi bi-plus-lg"></i> Nueva Venta
        </a>
        <button type="button" data-bs-toggle="modal" data-bs-target="#modalVoz"
                class="btn-luxor inline-flex items-center gap-2 rounded-full bg-luxor-danger px-5 py-2.5 text-sm font-semibold text-white hover:opacity-90">
            <i class="bi bi-mic-fill"></i> Venta por Voz
        </button>
    </div>
</div>

<div class="rounded-xl border border-luxor-border bg-luxor-surface shadow-sm">
    <div class="p-5">
        <div class="relative sm:max-w-xs">
            <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-sm text-luxor-muted"></i>
            <input id="buscarVenta" type="text" placeholder="Buscar venta, cajero..."
                   class="w-full rounded-full border border-luxor-border bg-luxor-surface2 py-2.5 pl-10 pr-4 text-sm outline-none focus:border-luxor-accent">
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-luxor-surface2 text-left text-xs text-luxor-muted">
                    <th class="px-5 py-3 font-semibold">#</th>
                    <th class="px-5 py-3 font-semibold">Fecha</th>
                    <th class="px-5 py-3 font-semibold">Cajero</th>
                    <th class="px-5 py-3 font-semibold">Productos</th>
                    <th class="px-5 py-3 font-semibold">Total</th>
                    <th class="px-5 py-3 font-semibold">Método Pago</th>
                    <th class="px-5 py-3 font-semibold">Estado</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody id="tablaVentas">
                @forelse($ventas as $venta)
                <tr class="border-t border-luxor-border hover:bg-luxor-surface2/50">
                    <td class="px-5 py-4"><strong>#{{ $venta->id }}</strong></td>
                    <td class="px-5 py-4 text-luxor-muted">{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-5 py-4">{{ $venta->usuario->nombre_completo }}</td>
                    <td class="px-5 py-4">
                        <span class="whitespace-nowrap rounded-full bg-luxor-surface2 px-3 py-1 text-xs">{{ $venta->detalles->count() }} producto(s)</span>
                    </td>
                    <td class="px-5 py-4"><strong class="whitespace-nowrap text-emerald-500">$ {{ number_format($venta->total_venta, 0) }}</strong></td>
                    <td class="px-5 py-4">
                        @if($venta->metodo_pago == 'Efectivo')
                            <span class="rounded-full bg-emerald-500/15 px-3 py-1 text-xs text-emerald-500">Efectivo</span>
                        @elseif($venta->metodo_pago == 'Tarjeta')
                            <span class="rounded-full bg-blue-500/15 px-3 py-1 text-xs text-blue-500">Tarjeta</span>
                        @else
                            <span class="rounded-full bg-purple-500/15 px-3 py-1 text-xs text-purple-500">Transferencia</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        @if($venta->estado == 'Completada')
                            <span class="rounded-full bg-emerald-500/15 px-3 py-1 text-xs text-emerald-500">Completada</span>
                        @else
                            <span class="rounded-full bg-red-500/15 px-3 py-1 text-xs text-red-500">{{ $venta->estado }}</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-right">
                        <div class="inline-flex gap-2">
                            <a href="{{ url('/ventas/' . $venta->id) }}" title="Ver detalle"
                               class="grid h-8 w-8 place-items-center rounded-lg bg-luxor-surface2 text-luxor-muted transition hover:bg-luxor-accent hover:text-white">
                                <i class="bi bi-eye"></i>
                            </a>
                            <form action="{{ url('/ventas/' . $venta->id) }}" method="POST"
                                  onsubmit="return confirm('¿Anular venta? Se devolverá el stock.')">
                                @csrf @method('DELETE')
                                <button type="submit" title="Anular"
                                    class="grid h-8 w-8 place-items-center rounded-lg bg-luxor-surface2 text-luxor-muted transition hover:bg-luxor-danger hover:text-white">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-12 text-center text-luxor-muted">
                        <i class="bi bi-cart-x display-6"></i><br>
                        <strong>No hay ventas registradas</strong><br>
                        <small>Registra tu primera venta</small>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($ventas->hasPages())
    <div class="p-4">{{ $ventas->links() }}</div>
    @endif
</div>

{{-- ============ MODAL VENTA POR VOZ (CARRITO MULTI-PRODUCTO) ============ --}}
<div class="modal fade" id="modalVoz" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-2xl" style="background: var(--lx-surface); color: var(--lx-text); border-radius: 1.25rem;">
            <div class="modal-header border-0 px-5 pb-0 pt-5">
                <h5 class="modal-title flex items-center gap-3 font-semibold">
                    <span class="grid h-10 w-10 place-items-center rounded-full bg-luxor-danger text-white shadow-md"><i class="bi bi-mic-fill"></i></span>
                    Venta por Voz
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="resetCarritoVozVentas()"></button>
            </div>
            <div class="modal-body px-5 pb-5">

                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-luxor-muted">Modo</label>
                        <select id="modo-voz-ventas"
                                class="w-full rounded-lg border px-3 py-2 text-sm outline-none focus:border-luxor-accent"
                                style="border-color: var(--lx-border); background: var(--lx-surface2);">
                            <option value="venta">🛒 Venta (descuenta stock)</option>
                            <option value="ingreso">📦 Ingreso (suma stock)</option>
                        </select>
                    </div>
                    <div id="bloque-metodo-pago">
                        <label class="mb-1 block text-xs font-semibold text-luxor-muted">Método de pago</label>
                        <select id="metodo-pago-voz"
                                class="w-full rounded-lg border px-3 py-2 text-sm outline-none focus:border-luxor-accent"
                                style="border-color: var(--lx-border); background: var(--lx-surface2);">
                            <option value="Efectivo">💵 Efectivo</option>
                            <option value="Tarjeta">💳 Tarjeta</option>
                            <option value="Transferencia">📲 Transferencia</option>
                        </select>
                    </div>
                </div>

                <div class="rounded-xl border p-4" style="border-color: var(--lx-border); background: var(--lx-surface2);">
                    <div class="mb-2 flex items-center justify-between">
                        <small class="text-xs text-luxor-muted">🎤 Dicta <strong>un producto a la vez</strong> y lo agrego al carrito</small>
                        <span id="contador-carrito-voz-ventas" class="rounded-full bg-luxor-accent px-2.5 py-0.5 text-xs font-bold text-white">0 items</span>
                    </div>

                    <div class="flex items-center gap-3">
                        <button id="btn-mic-ventas" type="button"
                                class="btn-luxor grid h-16 w-16 shrink-0 place-items-center rounded-full bg-luxor-danger text-white shadow-lg transition hover:scale-105">
                            <i class="bi bi-mic-fill fs-3"></i>
                        </button>
                        <div class="flex-1">
                            <p id="estado-mic-ventas" class="text-sm font-semibold">Presiona el micrófono y dicta</p>
                            <p class="text-xs text-luxor-muted">Ej: "2 whisky black label" o "llegaron 3 cervezas aguila"</p>
                        </div>
                    </div>

                    <div class="mt-3 flex gap-2">
                        <input type="text" id="texto-voz-ventas"
                               class="w-full rounded-lg border px-3 py-2 text-sm outline-none focus:border-luxor-accent"
                               style="border-color: var(--lx-border); background: var(--lx-surface);"
                               placeholder="O escribe aquí: 2 whisky black label">
                        <button type="button"
                                class="btn-luxor whitespace-nowrap rounded-lg bg-luxor-accent px-4 py-2 text-sm font-semibold text-white hover:bg-luxor-accentDark"
                                onclick="dictarProductoVozVentas(document.getElementById('texto-voz-ventas').value)">
                            <i class="bi bi-plus-lg"></i> Agregar
                        </button>
                    </div>
                </div>

                <div class="mt-4">
                    <h6 class="mb-2 text-sm font-semibold"><i class="bi bi-cart3 text-luxor-accent"></i> Productos dictados</h6>
                    <div id="carrito-voz-ventas-vacio" class="rounded-lg border border-dashed py-6 text-center text-sm text-luxor-muted" style="border-color: var(--lx-border);">
                        Aún no has dictado productos
                    </div>
                    <div id="carrito-voz-ventas-lista" class="space-y-2"></div>
                </div>

                <div id="ultima-interp-ventas" class="mt-3 rounded-lg px-3 py-2 text-xs" style="background: var(--lx-surface2); display: none;">
                    <strong>🎧 Última dictado:</strong> "<span id="texto-escuchado-ventas"></span>" → <span id="interpretacion-ventas"></span>
                </div>

                <div class="mt-4 flex gap-2">
                    <button type="button" id="btn-crear-voz-ventas" onclick="crearAccionVozVentas()" disabled
                            class="btn-luxor w-full rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-40">
                        <i class="bi bi-check2-circle"></i> <span id="btn-texto-ventas">Confirmar</span> (<span id="btn-contador-ventas">0</span>)
                    </button>
                    <button type="button" onclick="resetCarritoVozVentas()"
                            class="w-full rounded-lg border px-4 py-2.5 text-sm font-semibold text-luxor-muted" style="border-color: var(--lx-border);">
                        <i class="bi bi-trash"></i> Vaciar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="form-voz-ventas" action="{{ route('ventas.vozCarrito') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="tipo" id="form-ventas-tipo">
    <input type="hidden" name="metodo_pago" id="form-ventas-metodo">
    <div id="form-ventas-items"></div>
</form>
@endsection

@push('scripts')
<script>
    console.log('✅ Script de ventas cargado');

    function elV(id) { return document.getElementById(id); }

    var PRODUCTOS_VOZ = @json($productosVoz);
    var carritoVozVentas = [];

    var NUMEROS = {
        'UN':1,'UNA':1,'DOS':2,'TRES':3,'CUATRO':4,'CINCO':5,'SEIS':6,
        'SIETE':7,'OCHO':8,'NUEVE':9,'DIEZ':10,'ONCE':11,'DOCE':12,
        'TRECE':13,'CATORCE':14,'QUINCE':15,'VEINTE':20
    };

    function normVozV(t) {
        return t.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toUpperCase();
    }

    elV('buscarVenta') && elV('buscarVenta').addEventListener('input', function () {
        var q = this.value.toLowerCase();
        document.querySelectorAll('#tablaVentas tr').forEach(function (tr) {
            tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });

    elV('modo-voz-ventas') && elV('modo-voz-ventas').addEventListener('change', function () {
        var bloque = elV('bloque-metodo-pago');
        if (bloque) bloque.style.display = this.value === 'venta' ? '' : 'none';
    });

    // ============ MICRÓFONO ============
    function iniciarMicVentas() {
        var estado = elV('estado-mic-ventas');
        var SR = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SR) {
            if (estado) estado.textContent = '⚠️ Tu navegador no soporta voz. Usa Chrome o escribe abajo.';
            return;
        }
        try {
            var rec = new SR();
            rec.lang = 'es-CO';
            rec.interimResults = false;
            var respondio = false;
            if (estado) estado.innerHTML = '<span class="text-red-500">🔴 Escuchando... habla ahora</span>';

            var aviso = setTimeout(function () {
                if (!respondio && estado) {
                    estado.textContent = '🦁 El navegador no respondió al micrófono. Revisa el permiso (candado de la URL) o escribe abajo.';
                }
            }, 4000);

            rec.onresult = function (e) {
                respondio = true; clearTimeout(aviso);
                dictarProductoVozVentas(e.results[0][0].transcript);
            };
            rec.onerror = function (e) {
                respondio = true; clearTimeout(aviso);
                if (!estado) return;
                if (e.error === 'aborted' || e.error === 'no-speech') {
                    estado.textContent = '🎙️ No te escuché bien. Toca el micrófono y habla de nuevo.';
                } else if (e.error === 'not-allowed' || e.error === 'service-not-allowed') {
                    estado.textContent = '🔒 Micrófono bloqueado. Click en el candado de la URL → Permisos → Micrófono → Permitir.';
                } else if (e.error === 'network') {
                    estado.textContent = '📶 La voz necesita internet. Revisa tu conexión.';
                } else {
                    estado.textContent = '⚠️ Error de voz: ' + e.error + '. Usa Chrome o escribe abajo.';
                }
            };
            rec.onend = function () {
                clearTimeout(aviso);
                setTimeout(function () { if (estado) estado.textContent = 'Presiona el micrófono y dicta'; }, 1500);
            };
            rec.start();
        } catch (err) {
            console.error('Error al iniciar micrófono:', err);
            if (estado) estado.textContent = '⚠️ No se pudo abrir el micrófono. Usa Chrome o escribe abajo.';
        }
    }

    elV('btn-mic-ventas') && elV('btn-mic-ventas').addEventListener('click', iniciarMicVentas);

    // ============ INTERPRETAR ============
    function detectarIntencion(t) {
        var clavesVenta = ['VEND', 'VENTA'];
        var clavesIngreso = ['LLEG', 'AGREG', 'INGRES', 'SUMAR', 'ENTR', 'RECIB', 'COMPR', 'ABASTEC'];
        for (var i = 0; i < clavesIngreso.length; i++) if (t.includes(clavesIngreso[i])) return 'ingreso';
        for (var j = 0; j < clavesVenta.length; j++) if (t.includes(clavesVenta[j])) return 'venta';
        return null;
    }

    function dictarProductoVozVentas(texto) {
        if (!texto) return;
        var t = normVozV(texto);

        var cantidad = null;
        var m = t.match(/\d+/);
        if (m) cantidad = parseInt(m[0]);
        else {
            for (var pal in NUMEROS) { if (t.includes(pal)) { cantidad = NUMEROS[pal]; break; } }
        }
        if (!cantidad) cantidad = 1;

        var intencion = detectarIntencion(t);
        var modoSel = elV('modo-voz-ventas');
        if (intencion && modoSel) {
            modoSel.value = intencion;
            var bloque = elV('bloque-metodo-pago');
            if (bloque) bloque.style.display = intencion === 'venta' ? '' : 'none';
        }

        var stop = ['VENDIERON','VENDIO','VENTA','LLEGARON','LLEGO','LLEG','AGREGAR','AGREGA','INGRESAR','INGRESA','SUMAR','SUMA','ENTRAR','ENTRA','RECIBIR','RECIBE','COMPRAR','COMPRA','ABASTECER','ABASTECE','DE','A','Y','UN','UNA','UNAS','UNOS','BOTELLAS','BOTELLA','UNIDADES','UNIDAD','CAJAS','CAJA','POR','FAVOR','MAS'];
        var palabras = t.split(/\s+/).filter(function (w) { return w.length >= 3 && stop.indexOf(w) === -1 && !/^\d+$/.test(w); });

        var mejor = null, mejorScore = 0;
        PRODUCTOS_VOZ.forEach(function (p) {
            var nombre = normVozV(p.nombre_producto).split(/\s+/).map(function (w) { return w.replace(/ML$/, ''); }).filter(function (w) { return w.length >= 3; });
            var score = 0;
            palabras.forEach(function (q) {
                for (var k = 0; k < nombre.length; k++) {
                    if (nombre[k].indexOf(q) !== -1 || q.indexOf(nombre[k]) !== -1) { score++; break; }
                }
            });
            if (score > mejorScore) { mejorScore = score; mejor = p; }
        });

        var interpEl = elV('ultima-interp-ventas');
        var escuchadoEl = elV('texto-escuchado-ventas');
        var interpretadoEl = elV('interpretacion-ventas');

        if (!mejor) {
            if (escuchadoEl) escuchadoEl.textContent = texto;
            if (interpretadoEl) interpretadoEl.innerHTML = '<span class="text-red-500">❌ No entendí el producto. Intenta de nuevo.</span>';
            if (interpEl) interpEl.style.display = 'block';
            return;
        }

        var existente = null;
        for (var x = 0; x < carritoVozVentas.length; x++) if (carritoVozVentas[x].id === mejor.id) existente = carritoVozVentas[x];
        if (existente) existente.cantidad += cantidad;
        else carritoVozVentas.push({ id: mejor.id, nombre: mejor.nombre_producto, cantidad: cantidad, precio: parseFloat(mejor.precio_venta) || 0, stock: parseInt(mejor.stock_actual) || 0 });

        var modo = modoSel ? modoSel.value : 'venta';
        if (escuchadoEl) escuchadoEl.textContent = texto;
        if (interpretadoEl) interpretadoEl.innerHTML = '✅ Agregado (' + (modo === 'venta' ? '🛒 VENTA' : '📦 INGRESO') + '): <strong>' + cantidad + ' x ' + mejor.nombre_producto + '</strong>';
        if (interpEl) interpEl.style.display = 'block';

        var input = elV('texto-voz-ventas');
        if (input) input.value = '';
        renderCarritoVozVentas();
    }

    // ============ CARRITO (sin backticks, a prueba de pegado) ============
    function renderCarritoVozVentas() {
        var lista = elV('carrito-voz-ventas-lista');
        var vacio = elV('carrito-voz-ventas-vacio');
        var modo = elV('modo-voz-ventas') ? elV('modo-voz-ventas').value : 'venta';
        var btn = elV('btn-crear-voz-ventas');

        if (carritoVozVentas.length === 0) {
            if (vacio) vacio.style.display = 'block';
            if (lista) lista.innerHTML = '';
            if (btn) btn.disabled = true;
        } else {
            if (vacio) vacio.style.display = 'none';
            if (btn) btn.disabled = false;
            var html = '';
            for (var i = 0; i < carritoVozVentas.length; i++) {
                var item = carritoVozVentas[i];
                var colorTotal = (modo === 'venta') ? 'text-emerald-500' : 'text-blue-500';
                html += '<div class="flex items-center gap-3 rounded-lg border px-3 py-2" style="border-color: var(--lx-border); background: var(--lx-surface2);">'
                    + '<span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-blue-500/15 text-blue-500"><i class="bi bi-box-seam"></i></span>'
                    + '<div class="min-w-0 flex-1">'
                    + '<strong class="block truncate text-sm">' + item.nombre + '</strong>'
                    + '<div class="mt-1 flex items-center gap-2 text-xs">'
                    + '<button type="button" onclick="cambiarCantVozVentas(' + i + ', -1)" class="grid h-6 w-6 place-items-center rounded bg-luxor-surface hover:bg-luxor-accent hover:text-white">−</button>'
                    + '<strong class="w-8 text-center">' + item.cantidad + '</strong>'
                    + '<button type="button" onclick="cambiarCantVozVentas(' + i + ', 1)" class="grid h-6 w-6 place-items-center rounded bg-luxor-surface hover:bg-luxor-accent hover:text-white">+</button>'
                    + '<span class="ml-2 text-luxor-muted">x $ ' + item.precio.toLocaleString('es-CO') + '</span>'
                    + '</div></div>'
                    + '<strong class="text-sm ' + colorTotal + '">$ ' + (item.cantidad * item.precio).toLocaleString('es-CO') + '</strong>'
                    + '<button type="button" onclick="quitarVozVentas(' + i + ')" class="text-luxor-muted hover:text-red-500" title="Quitar"><i class="bi bi-trash"></i></button>'
                    + '</div>';
            }
            if (lista) lista.innerHTML = html;
        }

        var totalItems = 0;
        for (var j = 0; j < carritoVozVentas.length; j++) totalItems += carritoVozVentas[j].cantidad;
        var cont = elV('contador-carrito-voz-ventas');
        if (cont) cont.textContent = carritoVozVentas.length + ' items';
        var bc = elV('btn-contador-ventas');
        if (bc) bc.textContent = totalItems;
        var bt = elV('btn-texto-ventas');
        if (bt) bt.textContent = (modo === 'venta') ? '💵 Registrar Venta' : '📦 Confirmar Ingreso';
    }

    function cambiarCantVozVentas(idx, delta) {
        carritoVozVentas[idx].cantidad = Math.max(1, carritoVozVentas[idx].cantidad + delta);
        renderCarritoVozVentas();
    }

    function quitarVozVentas(idx) {
        carritoVozVentas.splice(idx, 1);
        renderCarritoVozVentas();
    }

    function resetCarritoVozVentas() {
        carritoVozVentas = [];
        renderCarritoVozVentas();
        var i = elV('ultima-interp-ventas');
        if (i) i.style.display = 'none';
    }

    function crearAccionVozVentas() {
        if (carritoVozVentas.length === 0) return;
        var modo = elV('modo-voz-ventas').value;
        document.getElementById('form-ventas-tipo').value = modo;
        document.getElementById('form-ventas-metodo').value = modo === 'venta' ? document.getElementById('metodo-pago-voz').value : 'Efectivo';
        var html = '';
        for (var i = 0; i < carritoVozVentas.length; i++) {
            html += '<input type="hidden" name="productos[' + i + '][id]" value="' + carritoVozVentas[i].id + '">'
                 + '<input type="hidden" name="productos[' + i + '][cantidad]" value="' + carritoVozVentas[i].cantidad + '">';
        }
        document.getElementById('form-ventas-items').innerHTML = html;
        document.getElementById('form-voz-ventas').submit();
    }

    // Exponer funciones a los onclick del HTML
    window.dictarProductoVozVentas = dictarProductoVozVentas;
    window.cambiarCantVozVentas = cambiarCantVozVentas;
    window.quitarVozVentas = quitarVozVentas;
    window.resetCarritoVozVentas = resetCarritoVozVentas;
    window.crearAccionVozVentas = crearAccionVozVentas;
</script>
@endpush