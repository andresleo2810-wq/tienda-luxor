@extends('layouts.app')

@section('title', 'Cerrar Caja')

@section('content')
<div class="mb-2 text-xs text-luxor-muted">Gestión / Caja / #{{ $caja->id }} / Cerrar</div>
<div class="mb-8">
    <h1 class="text-4xl font-semibold tracking-tight">Cerrar Caja #{{ $caja->id }}</h1>
    <p class="mt-2 text-sm text-luxor-muted">Abierta el {{ optional($caja->fecha_apertura)->format('d/m/Y H:i') }} por {{ optional($caja->usuario)->nombre_completo }}</p>
</div>

<div class="grid gap-5 xl:grid-cols-[1fr_1.2fr]">
    {{-- Resumen del turno (usa los datos del controlador) --}}
    <div class="space-y-5">
        <div class="rounded-xl border border-luxor-border bg-luxor-surface p-5 shadow-sm">
            <h2 class="mb-4 font-semibold"><i class="bi bi-receipt text-luxor-accent"></i> Resumen del turno</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-luxor-muted">Monto inicial</span>
                    <strong>$ {{ number_format($caja->monto_inicial, 0) }}</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-luxor-muted">Ventas en efectivo del turno</span>
                    <strong class="text-emerald-500">+ $ {{ number_format($ventasEfectivo, 0) }}</strong>
                </div>
                <div class="border-t border-dashed pt-3" style="border-color: var(--lx-border);">
                    <div class="flex justify-between text-base">
                        <span class="font-semibold">Efectivo esperado</span>
                        <strong id="esperado" data-valor="{{ $montoEsperado }}" class="text-luxor-accent">$ {{ number_format($montoEsperado, 0) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-dashed p-5 text-xs text-luxor-muted" style="border-color: var(--lx-border); background: var(--lx-surface2);">
            <i class="bi bi-lightbulb"></i> <strong>Tip:</strong> puedes escribir el monto con puntos (ej: 600.000); el sistema lo limpia solo al enviar.
        </div>
    </div>

    {{-- Formulario de cierre --}}
    <form id="formCerrar" action="{{ route('cajas.cerrar', $caja->id) }}" method="POST" class="h-fit rounded-xl border border-luxor-border bg-luxor-surface shadow-sm">
        @csrf
        @method('PUT')

        @if($errors->any())
        <div class="mx-5 mt-5 rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700">
            <strong><i class="bi bi-exclamation-triangle-fill"></i> No se pudo cerrar la caja:</strong>
            <ul class="mt-1 list-inside list-disc">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="border-b p-5" style="border-color: var(--lx-border);">
            <h2 class="font-semibold"><i class="bi bi-lock text-luxor-danger"></i> Arqueo de cierre</h2>
        </div>

        <div class="space-y-5 p-5">
            <div>
                <label class="mb-2 block text-sm font-medium">Monto final contado *</label>
                <div class="relative">
                    <i class="bi bi-cash-coin absolute left-4 top-1/2 -translate-y-1/2 text-luxor-muted"></i>
                    <input type="text" inputmode="numeric" name="monto_final_cierre" id="monto_final_cierre" required
                           oninput="calcDif()" value="{{ old('monto_final_cierre') }}"
                           class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 py-3 pl-11 pr-4 text-lg font-bold outline-none focus:border-luxor-accent"
                           placeholder="Ej: {{ number_format($montoEsperado, 0) }}">
                </div>
                @error('monto_final_cierre') <p class="mt-1 text-xs text-luxor-danger">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-between rounded-lg px-4 py-3" style="background: var(--lx-surface2);">
                <span class="text-sm text-luxor-muted">Diferencia (sobrante / faltante)</span>
                <strong id="diferencia" class="text-lg text-luxor-muted">$ 0</strong>
            </div>
        </div>

        <div class="flex justify-end gap-2 border-t p-5" style="border-color: var(--lx-border);">
            <a href="{{ route('cajas.index') }}"
               class="rounded-full border border-luxor-border bg-luxor-surface2 px-5 py-2.5 text-sm font-semibold text-luxor-muted hover:text-luxor-text">
                Cancelar
            </a>
            <button type="submit" onclick="return confirm('¿Cerrar la caja con el monto digitado?')"
                    class="btn-luxor inline-flex items-center gap-2 rounded-full bg-luxor-danger px-6 py-2.5 text-sm font-semibold text-white hover:opacity-90">
                <i class="bi bi-lock"></i> Cerrar Caja
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function limpiarMonto(v) { return (v || '').replace(/[$.\s]/g, ''); }

    function calcDif() {
        const esp = parseFloat(document.getElementById('esperado').dataset.valor) || 0;
        const fin = parseFloat(limpiarMonto(document.getElementById('monto_final_cierre').value)) || 0;
        const dif = fin - esp;
        const el = document.getElementById('diferencia');
        el.textContent = (dif >= 0 ? '+ $ ' : '- $ ') + Math.abs(dif).toLocaleString('es-CO');
        el.className = 'text-lg font-bold ' + (dif === 0 ? 'text-emerald-500' : 'text-amber-500');
    }

    // Limpia los puntos antes de enviar (600.000 → 600000)
    document.getElementById('formCerrar').addEventListener('submit', function () {
        document.getElementById('monto_final_cierre').value =
            limpiarMonto(document.getElementById('monto_final_cierre').value);
    });
</script>
@endpush