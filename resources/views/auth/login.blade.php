@extends('layouts.guest')

@section('title', 'Iniciar sesión')

@section('content')
<div class="w-full max-w-md">
    {{-- Selector de tema por color (círculos) --}}
    <div class="mb-6 flex justify-end">
        <div class="flex items-center gap-2 rounded-full border border-luxor-border bg-luxor-surface px-3 py-2 shadow-sm">
            <button type="button" onclick="setTheme('cobalto')" id="dot-cobalto"
                    class="dot" style="background:#2864D7" title="Tema Cobalto"></button>
            <button type="button" onclick="setTheme('sage')" id="dot-sage"
                    class="dot" style="background:#6E9F78" title="Tema Sage"></button>
            <button type="button" onclick="toggleDark()" id="dot-dark"
                    class="dot" style="background:#111B23" title="Modo oscuro"></button>
        </div>
    </div>

    {{-- Card principal --}}
    <div class="rounded-2xl border border-luxor-border bg-luxor-surface p-8 shadow-xl">
        {{-- Logo --}}
        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-2xl bg-gradient-to-br from-luxor-accent to-luxor-blue text-3xl font-bold italic text-white shadow-lg">
                L
            </div>
            <h1 class="text-2xl font-semibold">Tienda Luxor</h1>
            <p class="mt-1 text-sm text-luxor-muted">Gestión inteligente de licorería</p>
        </div>

        {{-- Formulario --}}
        <form action="{{ route('login') }}" method="POST" class="space-y-5">
            @csrf

                       <div>
                            <label for="email" class="mb-2 block text-sm font-medium">Usuario o correo</label>
                            <div class="relative">
                                <i class="bi bi-person-circle absolute left-4 top-1/2 -translate-y-1/2 text-luxor-muted"></i>
                                <input type="text" name="usuario_login" id="usuario_login" required value="{{ old('usuario_login') }}"
                                autocomplete="username"
                                class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 py-3 pl-11 pr-4 text-sm outline-none transition focus:border-luxor-accent focus:ring-2 focus:ring-luxor-accent/20"
                                placeholder="admin / cajero">
                            </div>
                            @error('email')
                                <p class="mt-1 text-xs text-luxor-danger">{{ $message }}</p>
                            @enderror
                        </div>

            <div>
                <label for="password" class="mb-2 block text-sm font-medium">Contraseña</label>
                <div class="relative">
                    <i class="bi bi-lock absolute left-4 top-1/2 -translate-y-1/2 text-luxor-muted"></i>
                    <input type="password" name="password" id="password" required
                           class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 py-3 pl-11 pr-4 text-sm outline-none transition focus:border-luxor-accent focus:ring-2 focus:ring-luxor-accent/20"
                           placeholder="••••••••">
                </div>
                @error('password')
                    <p class="mt-1 text-xs text-luxor-danger">{{ $message }}</p>
                @enderror
            </div>

            @if(session('error'))
                <div class="rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
                </div>
            @endif

            <button type="submit"
                    class="w-full rounded-lg bg-luxor-accent px-4 py-3 text-sm font-semibold text-white shadow-lg transition hover:bg-luxor-accentDark focus:ring-2 focus:ring-luxor-accent/40">
                <i class="bi bi-box-arrow-in-right"></i> Iniciar sesión
            </button>
        </form>

        <div class="mt-8 border-t border-luxor-border pt-6 text-center text-xs text-luxor-muted">
            <p>© {{ date('Y') }} Tienda Luxor. Todos los derechos reservados.</p>
            <p class="mt-1">
                <i class="bi bi-shield-check text-luxor-accent"></i>
                Conexión segura y cifrada
            </p>
        </div>
    </div>

    <p class="mt-6 text-center text-xs text-luxor-muted">
        Desarrollado con Laravel 11 + Bootstrap 5 + Tailwind CSS
    </p>
</div>
@endsection