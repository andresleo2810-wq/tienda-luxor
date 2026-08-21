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

{{-- ============ MODAL VENTA POR VOZ ============ --}}
<div class="modal fade" id="modalVoz" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl" style="background: var(--lx-surface); color: var(--lx-text); border-radius: 1.25rem;">
            <div class="modal-header border-0 px-5 pb-0 pt-5">
                <h5 class="modal-title flex items-center gap-3 font-semibold">
                    <span class="grid h-10 w-10 place-items-center rounded-full bg-luxor-danger text-white shadow-md"><i class="bi bi-mic-fill"></i></span>
                    Venta por Voz
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-5 pb-5 text-center">
                <div class="mx-auto mb-4 max-w-sm rounded-xl px-4 py-3 text-xs text-luxor-muted" style="background: var(--lx-surface2);">
                    🛒 <em>"Se vendieron 2 botellas de Whisky Black Label"</em> → descuenta<br>
                    📦 <em>"Llegaron 3 Whisky Black Label"</em> → suma al inventario
                </div>

                <button id="btn-mic"
                        class="btn-luxor mx-auto my-2 grid h-24 w-24 place-items-center rounded-full bg-luxor-danger text-white shadow-xl transition hover:scale-105">
                    <i class="bi bi-mic-fill fs-2"></i>
                </button>
                <p id="estado-mic" class="mt-3 text-xs text-luxor-muted">Presiona el micrófono y habla</p>

                <div class="mt-5 rounded-xl border p-4" style="border-color: var(--lx-border);">
                    <small class="text-luxor-muted">💻 <strong>¿Sin micrófono?</strong> Escribe el comando:</small>
                    <div class="mt-2 flex gap-2">
                        <input type="text" id="texto-prueba"
                               class="w-full rounded-lg border px-3 py-2 text-sm outline-none focus:border-luxor-accent"
                               style="border-color: var(--lx-border); background: var(--lx-surface2);"
                               placeholder="Ej: se vendieron 2 botellas de Whisky Black Label">
                        <button type="button"
                                class="btn-luxor whitespace-nowrap rounded-lg bg-luxor-accent px-4 py-2 text-sm font-semibold text-white hover:bg-luxor-accentDark"
                                onclick="procesarVoz(document.getElementById('texto-prueba').value)">
                            <i class="bi bi-keyboard"></i> Procesar
                        </button>
                    </div>
                </div>

                <div id="resultado-voz" class="mt-4" style="display: none;">
                    <p class="mb-2 text-sm"><strong>🎧 Escuché:</strong> "<span id="texto-escuchado"></span>"</p>
                    <div id="interpretacion" class="alert alert-success small"></div>

                    <form action="{{ route('ventas.voz') }}" method="POST" id="form-voz">
                        @csrf
                        <input type="hidden" name="id_producto" id="voz-producto-id">
                        <input type="hidden" name="cantidad" id="voz-cantidad">
                        <input type="hidden" name="tipo" id="voz-tipo" value="venta">
                        <div class="flex gap-2">
                            <button type="submit"
                                    class="btn-luxor w-full rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">
                                ✅ Confirmar
                            </button>
                            <button type="button" onclick="cancelarVoz()"
                                    class="w-full rounded-lg border px-4 py-2.5 text-sm font-semibold text-luxor-muted transition hover:text-luxor-danger"
                                    style="border-color: var(--lx-border);">
                                ❌ Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Búsqueda en vivo
    document.getElementById('buscarVenta').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#tablaVentas tr').forEach(tr => {
            tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });

    // ============ LÓGICA DE VOZ (sin cambios) ============
    const PRODUCTOS_VOZ = @json($productosVoz);

    const NUMEROS = {
        'UN':1,'UNA':1,'DOS':2,'TRES':3,'CUATRO':4,'CINCO':5,'SEIS':6,
        'SIETE':7,'OCHO':8,'NUEVE':9,'DIEZ':10,'ONCE':11,'DOCE':12,
        'TRECE':13,'CATORCE':14,'QUINCE':15,'VEINTE':20
    };

    function normalizarVoz(t) {
        return t.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toUpperCase();
    }

    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

    document.getElementById('btn-mic').addEventListener('click', function() {
        if (!SpeechRecognition) {
            alert('Usa Google Chrome o Edge para el reconocimiento de voz.');
            return;
        }
        const rec = new SpeechRecognition();
        rec.lang = 'es-CO';
        rec.interimResults = false;
        document.getElementById('estado-mic').innerHTML =
            '<span class="text-danger">🔴 Escuchando... habla ahora</span>';
        rec.onresult = function(e) { procesarVoz(e.results[0][0].transcript); };
        rec.onerror = function(e) {
            document.getElementById('estado-mic').innerHTML = '❌ Error: ' + e.error;
        };
        rec.onend = function() {
            setTimeout(() => {
                document.getElementById('estado-mic').textContent = 'Presiona el micrófono y habla';
            }, 2000);
        };
        rec.start();
    });

    function detectarIntencion(t) {
        const clavesVenta = ['VEND', 'VENTA'];
        const clavesIngreso = ['LLEG', 'AGREG', 'INGRES', 'SUMAR', 'ENTR', 'RECIB', 'COMPR', 'ABASTEC'];
        if (clavesIngreso.some(k => t.includes(k))) return 'ingreso';
        if (clavesVenta.some(k => t.includes(k))) return 'venta';
        return 'venta';
    }

    function procesarVoz(texto) {
        const t = normalizarVoz(texto);

        let cantidad = null;
        const m = t.match(/\d+/);
        if (m) {
            cantidad = parseInt(m[0]);
        } else {
            for (const [pal, num] of Object.entries(NUMEROS)) {
                if (t.includes(pal)) { cantidad = num; break; }
            }
        }
        if (!cantidad) cantidad = 1;

        const tipo = detectarIntencion(t);

        let mejor = null, mejorScore = 0;
        PRODUCTOS_VOZ.forEach(p => {
            const palabras = normalizarVoz(p.nombre_producto)
                .split(/\s+/)
                .map(w => w.replace(/ML$/, ''))
                .filter(w => w.length >= 3);
            const score = palabras.filter(w => t.includes(w)).length;
            if (score > mejorScore) { mejorScore = score; mejor = p; }
        });

        document.getElementById('texto-escuchado').textContent = texto;
        document.getElementById('resultado-voz').style.display = 'block';

        if (mejor && mejorScore >= 1) {
            const esVenta = (tipo === 'venta');
            document.getElementById('interpretacion').className =
                esVenta ? 'alert alert-success small' : 'alert alert-info small';
            document.getElementById('interpretacion').innerHTML = esVenta
                ? 'Interpreté: <strong>VENDER ' + cantidad + ' x ' + mejor.nombre_producto + '</strong> (descuenta stock)'
                : 'Interpreté: <strong>INGRESAR ' + cantidad + ' x ' + mejor.nombre_producto + '</strong> (suma stock)';
            document.getElementById('voz-producto-id').value = mejor.id;
            document.getElementById('voz-cantidad').value = cantidad;
            document.getElementById('voz-tipo').value = tipo;
            document.getElementById('form-voz').style.display = 'block';
        } else {
            document.getElementById('interpretacion').className = 'alert alert-warning small';
            document.getElementById('interpretacion').innerHTML = 'No entendí el producto. Di el nombre completo.';
            document.getElementById('form-voz').style.display = 'none';
        }
    }

    function cancelarVoz() {
        document.getElementById('resultado-voz').style.display = 'none';
        document.getElementById('texto-prueba').value = '';
        document.getElementById('estado-mic').textContent = 'Presiona el micrófono y habla';
    }
</script>
@endpush