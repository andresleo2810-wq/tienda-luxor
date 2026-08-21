@extends('layouts.app')

@section('title', 'Abrir Caja')

@section('content')
<div class="mb-2 text-xs text-luxor-muted">Gestión / Caja / Abrir</div>
<div class="mb-8">
    <h1 class="text-4xl font-semibold tracking-tight">Abrir Caja</h1>
    <p class="mt-2 text-sm text-luxor-muted">Registra el monto inicial con el que inicia el turno.</p>
</div>

<div class="mx-auto max-w-lg">
    @if($cajaAbierta)
<div class="mb-5 rounded-lg border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-800">
    <i class="bi bi-exclamation-triangle-fill"></i>
    Ya hay una caja abierta (<strong>#{{ $cajaAbierta->id }}</strong>). Debes cerrarla antes de abrir otra.
    <a href="{{ route('cajas.cerrar.form', $cajaAbierta->id) }}" class="font-semibold underline">Cerrarla ahora →</a>
</div>
@endif
    <form action="{{ route('cajas.store') }}" method="POST" class="rounded-xl border border-luxor-border bg-luxor-surface shadow-sm">
        @csrf
        <div class="border-b p-5 text-center" style="border-color: var(--lx-border);">
            <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-emerald-500/15 text-2xl text-emerald-500"><i class="bi bi-unlock"></i></span>
            <h2 class="mt-3 font-semibold">Apertura de caja</h2>
            <p class="mt-1 text-xs text-luxor-muted">
                <i class="bi bi-person"></i> {{ Auth::user()->nombre_completo }} ·
                <i class="bi bi-calendar3"></i> {{ now()->format('d/m/Y H:i') }}
            </p>
        </div>

        <div class="p-5">
            <label class="mb-2 block text-sm font-medium">Monto inicial en efectivo *</label>
            <div class="relative">
                <i class="bi bi-cash-stack absolute left-4 top-1/2 -translate-y-1/2 text-luxor-muted"></i>
                <input type="number" name="monto_inicial" step="any" min="0" required
                       value="{{ old('monto_inicial') }}"
                       class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 py-3 pl-11 pr-4 text-lg font-bold outline-none focus:border-luxor-accent"
                       placeholder="Ej: 200000">
            </div>
            @error('monto_inicial')
                        <div class="mt-4">
                            <label class="mb-2 block text-sm font-medium">Notas <span class="text-luxor-muted">(opcional)</span></label>
                            <textarea name="notas" rows="2"
                                    class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 px-4 py-3 text-sm outline-none focus:border-luxor-accent"
                                    placeholder="Notas de la apertura...">{{ old('notas') }}</textarea>
                        </div>
             <p class="mt-1 text-xs text-luxor-danger">{{ $message }}</p> @enderror
            <p class="mt-2 text-xs text-luxor-muted"><i class="bi bi-info-circle"></i> Este monto será la base para el arqueo de cierre.</p>
        </div>

        <div class="flex justify-end gap-2 border-t p-5" style="border-color: var(--lx-border);">
            <a href="{{ route('cajas.index') }}"
               class="rounded-full border border-luxor-border bg-luxor-surface2 px-5 py-2.5 text-sm font-semibold text-luxor-muted hover:text-luxor-text">
                Cancelar
            </a>
            <button type="submit"
                    class="btn-luxor inline-flex items-center gap-2 rounded-full bg-luxor-accent px-6 py-2.5 text-sm font-semibold text-white hover:bg-luxor-accentDark">
                <i class="bi bi-check2-circle"></i> Abrir Caja
            </button>
        </div>
    </form>
</div>
@endsection