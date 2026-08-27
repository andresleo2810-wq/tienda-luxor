@extends('layouts.app')

@section('title', 'Detalle de Pedido')

@section('content')
<div class="mb-2 text-xs text-luxor-muted">Gestión / Pedidos / #{{ $pedido->id }}</div>
<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="flex flex-wrap items-center gap-3 text-4xl font-semibold tracking-tight">
            Pedido #{{ $pedido->id }}
            @if($pedido->estado == 'Pendiente')
                <span class="rounded-full bg-amber-500/15 px-3 py-1 text-sm text-amber-500">Pendiente</span>
            @elseif($pedido->estado == 'Recibido')
                <span class="rounded-full bg-emerald-500/15 px-3 py-1 text-sm text-emerald-500">Recibido</span>
            @elseif($pedido->estado == 'Parcial')
                <span class="rounded-full bg-blue-500/15 px-3 py-1 text-sm text-blue-500">Parcial</span>
            @else
                <span class="rounded-full bg-red-500/15 px-3 py-1 text-sm text-red-500">Cancelado</span>
            @endif
        </h1>
        <p class="mt-2 text-sm text-luxor-muted">{{ $pedido->proveedor->nombre }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('pedidos.index') }}"
           class="btn-luxor inline-flex items-center gap-2 rounded-full border border-luxor-border bg-luxor-surface px-5 py-2.5 text-sm font-semibold text-luxor-muted hover:text-luxor-text">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
        @if($pedido->estado == 'Pendiente' && $pedido->texto_ocr)
        <a href="{{ route('pedidos.verificar', $pedido->id) }}"
           class="btn-luxor inline-flex items-center gap-2 rounded-full bg-luxor-accent px-5 py-2.5 text-sm font-semibold text-white hover:bg-luxor-accentDark">
            <i class="bi bi-check2-all"></i> Verificar cantidades
        </a>
        @endif
    </div>
</div>

<div class="grid gap-5 xl:grid-cols-[1.6fr_1fr]">
    <div class="space-y-5">
        {{-- Productos --}}
        <div class="rounded-xl border border-luxor-border bg-luxor-surface shadow-sm">
            <div class="border-b p-5" style="border-color: var(--lx-border);">
                <h2 class="font-semibold"><i class="bi bi-box-seam text-luxor-accent"></i> Productos del pedido</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-luxor-surface2 text-left text-xs text-luxor-muted">
                            <th class="px-5 py-3 font-semibold">Producto</th>
                            <th class="px-5 py-3 font-semibold">Cantidad</th>
                            <th class="px-5 py-3 font-semibold">Costo unit.</th>
                            <th class="px-5 py-3 text-right font-semibold">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pedido->detalles as $detalle)
                        <tr class="border-t border-luxor-border">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-blue-500/15 text-blue-500"><i class="bi bi-box-seam"></i></span>
                                    <strong>{{ $detalle->producto->nombre_producto }}</strong>
                                </div>
                            </td>
                            <td class="px-5 py-4"><strong>{{ $detalle->cantidad }}</strong> <span class="text-xs text-luxor-muted">un.</span></td>
                            <td class="px-5 py-4 whitespace-nowrap text-luxor-muted">$ {{ number_format($detalle->costo_unitario, 0) }}</td>
                            <td class="px-5 py-4 whitespace-nowrap text-right font-semibold">$ {{ number_format($detalle->subtotal, 0) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-luxor-border" style="background: var(--lx-surface2);">
                            <td colspan="3" class="px-5 py-4 text-right text-sm font-semibold text-luxor-muted">TOTAL</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right text-lg font-bold text-emerald-500">$ {{ number_format($pedido->total_pedido, 0) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Análisis IA --}}
        @if($pedido->texto_ocr && $analisis)
        <div class="rounded-xl border border-luxor-border bg-luxor-surface shadow-sm" style="border-left: 4px solid var(--lx-accent);">
            <div class="border-b p-5" style="border-color: var(--lx-border);">
                <h2 class="font-semibold"><i class="bi bi-cpu text-luxor-accent"></i> Análisis IA de la factura (OCR)</h2>
                <p class="mt-1 text-xs text-luxor-muted">Comparativo entre lo pedido y lo detectado en la factura.</p>
            </div>
            <div class="space-y-3 p-5">
                @foreach($analisis as $item)
                <div class="flex items-center justify-between rounded-lg px-4 py-3" style="background: var(--lx-surface2);">
                    <span class="text-sm">
                        <strong>{{ $item['producto'] }}</strong>
                        <small class="text-luxor-muted">(x{{ $item['cantidad'] }})</small>
                    </span>
                    @if($item['detectado'])
                        <span class="rounded-full bg-emerald-500/15 px-3 py-1 text-xs text-emerald-500">✅ Detectado en factura</span>
                    @else
                        <span class="rounded-full bg-amber-500/15 px-3 py-1 text-xs text-amber-500">⚠️ No detectado</span>
                    @endif
                </div>
                @endforeach

                {{-- 📄 TODOS LOS PRODUCTOS LEÍDOS POR LA IA --}}
                @if(!empty($lineas))
                <div class="mt-4 rounded-lg border border-luxor-border px-4 py-3" style="background: var(--lx-surface2);">
                    <strong class="text-sm"><i class="bi bi-cpu text-luxor-accent"></i> 📄 Productos que la IA leyó en la factura ({{ count($lineas) }}):</strong>
                    <div class="mt-3 space-y-2">
                        @foreach($lineas as $linea)
                        <div class="flex items-start justify-between gap-3 rounded-lg border px-3 py-2 text-xs" style="border-color: var(--lx-border); background: var(--lx-surface);">
                            <div class="min-w-0 flex-1">
                                <strong class="block truncate">{{ $linea['nombre'] }}</strong>
                                <div class="mt-1 flex flex-wrap gap-2 text-[10px] text-luxor-muted">
                                    <span class="rounded bg-blue-500/15 px-1.5 py-0.5 text-blue-600"><strong>{{ $linea['cantidad'] }} x</strong></span>
                                    @if($linea['volumen'])
                                        <span class="rounded bg-purple-500/15 px-1.5 py-0.5 text-purple-600">{{ $linea['volumen'] }}</span>
                                    @endif
                                    @if($linea['grado'])
                                        <span class="rounded bg-orange-500/15 px-1.5 py-0.5 text-orange-600">{{ $linea['grado'] }}</span>
                                    @endif
                                    @if($linea['codigo'])
                                        <span class="rounded bg-gray-500/15 px-1.5 py-0.5 text-gray-600">Cód: {{ $linea['codigo'] }}</span>
                                    @endif
                                    @if($linea['precio'])
                                        <span class="rounded bg-emerald-500/15 px-1.5 py-0.5 text-emerald-600">$ {{ number_format($linea['precio'], 0) }}</span>
                                    @endif
                                </div>
                            </div>
                            @if($linea['existe'])
                                <span class="whitespace-nowrap rounded-full bg-emerald-500/15 px-2 py-0.5 text-[10px] text-emerald-500">✅ En catálogo</span>
                            @else
                                <span class="whitespace-nowrap rounded-full bg-blue-500/15 px-2 py-0.5 text-[10px] text-blue-500">🆕 Nuevo</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @if($pedido->estado == 'Pendiente')
                    <a href="{{ route('pedidos.verificar', $pedido->id) }}"
                       class="mt-3 inline-block rounded-lg bg-luxor-accent px-4 py-2 text-xs font-semibold text-white hover:bg-luxor-accentDark">
                        ✨ Crear los nuevos → Ir a verificación
                    </a>
                    @endif
                </div>
                @endif

                <details class="mt-2">
                    <summary class="cursor-pointer text-sm font-semibold text-luxor-accent">Ver texto extraído por la IA</summary>
                    <pre class="mt-2 max-h-64 overflow-auto rounded-lg border p-3 text-xs" style="border-color: var(--lx-border); background: var(--lx-surface2);">{{ $pedido->texto_ocr }}</pre>
                </details>
            </div>
        </div>
        @elseif($pedido->ruta_factura && !$pedido->texto_ocr)
        <div class="rounded-lg border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            <i class="bi bi-wifi-off"></i> La factura se guardó, pero no se pudo conectar con la IA (verifica tu conexión a internet).
        </div>
        @endif
    </div>

    {{-- Columna derecha --}}
    <div class="space-y-5">
        {{-- Información --}}
        <div class="rounded-xl border border-luxor-border bg-luxor-surface p-5 shadow-sm">
            <h2 class="mb-4 font-semibold"><i class="bi bi-info-circle text-luxor-accent"></i> Información</h2>
            <div class="space-y-3 text-sm">
                <p><span class="text-luxor-muted">Proveedor:</span> <strong>{{ $pedido->proveedor->nombre }}</strong></p>
                <p><span class="text-luxor-muted">Fecha:</span> <strong>{{ $pedido->fecha_pedido->format('d/m/Y') }}</strong></p>
                <p><span class="text-luxor-muted">Solicitó:</span> <strong>{{ $pedido->usuario->nombre_completo }}</strong></p>
                @if($pedido->observaciones)
                <p><span class="text-luxor-muted">Observaciones:</span> {{ $pedido->observaciones }}</p>
                @endif
            </div>
        </div>

        {{-- Factura guardada --}}
        @if($pedido->ruta_factura)
        <div class="rounded-xl border border-luxor-border bg-luxor-surface p-5 shadow-sm">
            <h2 class="mb-4 font-semibold"><i class="bi bi-file-earmark-image text-luxor-accent"></i> Factura adjunta</h2>
            <a href="{{ asset('storage/' . $pedido->ruta_factura) }}" target="_blank">
                <img src="{{ asset('storage/' . $pedido->ruta_factura) }}" class="w-full rounded-lg border" style="border-color: var(--lx-border);">
            </a>
            <p class="mt-2 text-xs text-luxor-muted">📅 Recibido: {{ optional($pedido->fecha_recepcion)->format('d/m/Y H:i') }} · Click para ampliar</p>
        </div>
        @endif

        {{-- Recepción con cámara --}}
        @if($pedido->estado == 'Pendiente')
        <div class="rounded-xl border border-luxor-border bg-luxor-surface p-5 shadow-sm" style="border-top: 4px solid var(--lx-accent);">
            <h2 class="mb-4 font-semibold"><i class="bi bi-camera text-luxor-accent"></i> Recepción con factura</h2>
            <form action="{{ url('/pedidos/' . $pedido->id . '/recibir') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <label class="mb-2 block text-sm font-medium">📷 Foto de la factura *</label>
                <input type="file" name="factura" accept="image/*" capture="environment" required
                       class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 px-4 py-3 text-sm outline-none focus:border-luxor-accent">
                <div id="preview-container" class="mt-3 text-center" style="display:none;">
                    <img id="preview-factura" class="mx-auto max-h-48 rounded-lg border" style="border-color: var(--lx-border);">
                    <p class="mt-1 text-xs text-emerald-500"><i class="bi bi-check-circle-fill"></i> Imagen lista (<span id="preview-peso"></span>)</p>
                </div>
                <p class="mt-2 text-xs text-luxor-muted">💡 En celular se abre la cámara. En PC eliges un archivo.</p>
                <button type="submit"
                        onclick="return confirm('¿Recibir pedido? La IA analizará la factura.')"
                        class="btn-luxor mt-4 w-full rounded-lg bg-luxor-accent px-4 py-3 text-sm font-semibold text-white hover:bg-luxor-accentDark">
                    <i class="bi bi-check2-all"></i> Recibir y analizar con IA
                </button>
            </form>
            <form action="{{ url('/pedidos/' . $pedido->id . '/cancelar') }}" method="POST" class="mt-2">
                @csrf
                @method('PUT')
                <button type="submit" onclick="return confirm('¿Cancelar este pedido?')"
                        class="w-full rounded-lg border border-luxor-border px-4 py-2.5 text-sm font-semibold text-luxor-muted transition hover:text-luxor-danger">
                    <i class="bi bi-x-circle"></i> Cancelar pedido
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelector('input[name="factura"]')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        document.getElementById('preview-peso').textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
        const reader = new FileReader();
        reader.onload = function(ev) {
            document.getElementById('preview-factura').src = ev.target.result;
            document.getElementById('preview-container').style.display = 'block';
        };
        reader.readAsDataURL(file);
    });
</script>
@endpush