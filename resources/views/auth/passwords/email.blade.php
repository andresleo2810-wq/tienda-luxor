@extends('layouts.guest')

@section('title', 'Recuperar contraseña')

@section('content')
<div class="mx-auto max-w-md rounded-2xl border border-luxor-border bg-luxor-surface p-8 shadow-xl">
    <h1 class="text-2xl font-semibold"> ¿Olvidaste tu contraseña?</h1>
    <p class="mt-2 text-sm text-luxor-muted">Escribe tu correo y te enviaremos un enlace.</p>

    @if(session('success'))
    <div class="mt-4 rounded-lg bg-emerald-500/15 px-4 py-3 text-sm text-emerald-600">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mt-4 rounded-lg bg-red-500/15 px-4 py-3 text-sm text-red-500">
        {{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="mt-5 space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium">Correo electrónico</label>
            <input type="email" name="correo" value="{{ old('correo') }}" required 
                   class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 px-4 py-3 text-sm outline-none focus:border-luxor-accent">
        </div>
        <button class="btn-luxor w-full rounded-lg bg-luxor-accent px-4 py-3 text-sm font-semibold text-white">
             Enviar enlace
        </button>
    </form>
    <a href="{{ route('login') }}" class="mt-4 block text-center text-xs text-luxor-muted">← Volver al login</a>
</div>
@endsection