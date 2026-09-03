@extends('layouts.guest')

@section('title', 'Nueva contraseña')

@section('content')
<div class="mx-auto max-w-md rounded-2xl border border-luxor-border bg-luxor-surface p-8 shadow-xl">
    <h1 class="text-2xl font-semibold">🔒 Nueva contraseña</h1>
    <p class="mt-2 text-sm text-luxor-muted">Crea tu nueva contraseña para <strong>{{ $correo }}</strong></p>

    @if($errors->any())
    <div class="mt-4 rounded-lg bg-red-500/15 px-4 py-3 text-sm text-red-500">
        {{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="mt-5 space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="correo" value="{{ $correo }}">

        <div>
            <label class="block text-sm font-medium">Nueva contraseña</label>
            <input type="password" name="password" required minlength="6"
                   class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 px-4 py-3 text-sm outline-none focus:border-luxor-accent">
        </div>

        <div>
            <label class="block text-sm font-medium">Confirmar contraseña</label>
            <input type="password" name="password_confirmation" required minlength="6"
                   class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 px-4 py-3 text-sm outline-none focus:border-luxor-accent">
        </div>

        <button class="btn-luxor w-full rounded-lg bg-luxor-accent px-4 py-3 text-sm font-semibold text-white">
            Guardar nueva contraseña
        </button>
    </form>
    <a href="{{ route('login') }}" class="mt-4 block text-center text-xs text-luxor-muted">← Volver al login</a>
</div>
@endsection