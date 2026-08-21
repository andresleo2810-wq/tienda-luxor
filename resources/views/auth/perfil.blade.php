@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('content')
<div class="mb-2 text-xs text-luxor-muted">Sistema / Mi Perfil</div>
<div class="mb-8">
    <h1 class="text-4xl font-semibold tracking-tight">Mi Perfil</h1>
    <p class="mt-2 text-sm text-luxor-muted">Consulta tus datos y actualiza tu contraseña.</p>
</div>

<div class="grid gap-5 xl:grid-cols-[1fr_1.4fr]">
    {{-- Tarjeta de identidad --}}
    <div class="h-fit rounded-xl border border-luxor-border bg-luxor-surface p-6 text-center shadow-sm">
        <span class="mx-auto grid h-20 w-20 place-items-center rounded-full bg-gradient-to-br from-luxor-accent to-luxor-blue text-3xl font-bold text-white shadow-lg">
            {{ strtoupper(substr($usuario->nombre_completo, 0, 1)) }}
        </span>
        <h2 class="mt-4 text-lg font-semibold">{{ $usuario->nombre_completo }}</h2>
        <p class="text-sm text-luxor-muted">{{ '@' . $usuario->usuario_login }}</p>
        <div class="mt-3 flex justify-center gap-2">
            @if($usuario->rol->nombre_rol == 'Administrador')
                <span class="rounded-full bg-purple-500/15 px-3 py-1 text-xs text-purple-500">{{ $usuario->rol->nombre_rol }}</span>
            @else
                <span class="rounded-full bg-blue-500/15 px-3 py-1 text-xs text-blue-500">{{ $usuario->rol->nombre_rol }}</span>
            @endif
            @if($usuario->estado)
                <span class="rounded-full bg-emerald-500/15 px-3 py-1 text-xs text-emerald-500">Activo</span>
            @endif
        </div>
        <div class="mt-5 border-t pt-4 text-xs text-luxor-muted" style="border-color: var(--lx-border);">
            <p><i class="bi bi-calendar3"></i> En el sistema desde: {{ optional($usuario->created_at)->format('d/m/Y') }}</p>
        </div>
    </div>

    {{-- Formulario de seguridad --}}
    <form action="{{ route('perfil.update') }}" method="POST" class="rounded-xl border border-luxor-border bg-luxor-surface shadow-sm">
        @csrf
        @method('PUT')

        <div class="border-b p-5" style="border-color: var(--lx-border);">
            <h2 class="font-semibold"><i class="bi bi-shield-lock text-luxor-accent"></i> Seguridad de la cuenta</h2>
            <p class="mt-1 text-xs text-luxor-muted">Para aplicar cambios debes confirmar tu contraseña actual.</p>
        </div>

        <div class="grid gap-5 p-5 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="mb-2 block text-sm font-medium">Nombre completo</label>
                <div class="relative">
                    <i class="bi bi-person absolute left-4 top-1/2 -translate-y-1/2 text-luxor-muted"></i>
                    <input type="text" name="nombre_completo" value="{{ old('nombre_completo', $usuario->nombre_completo) }}"
                           class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 py-3 pl-11 pr-4 text-sm outline-none focus:border-luxor-accent">
                </div>
            </div>

            <div class="sm:col-span-2">
                <label class="mb-2 block text-sm font-medium">Contraseña actual *</label>
                <div class="relative">
                    <i class="bi bi-lock absolute left-4 top-1/2 -translate-y-1/2 text-luxor-muted"></i>
                    <input type="password" name="password_actual" required
                           class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 py-3 pl-11 pr-4 text-sm outline-none focus:border-luxor-accent"
                           placeholder="Confirma tu contraseña">
                </div>
                @error('password_actual') <p class="mt-1 text-xs text-luxor-danger">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium">Nueva contraseña</label>
                <div class="relative">
                    <i class="bi bi-key absolute left-4 top-1/2 -translate-y-1/2 text-luxor-muted"></i>
                    <input type="password" name="password"
                           class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 py-3 pl-11 pr-4 text-sm outline-none focus:border-luxor-accent"
                           placeholder="Vacía = no cambiar">
                </div>
                @error('password') <p class="mt-1 text-xs text-luxor-danger">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium">Confirmar nueva contraseña</label>
                <div class="relative">
                    <i class="bi bi-key-fill absolute left-4 top-1/2 -translate-y-1/2 text-luxor-muted"></i>
                    <input type="password" name="password_confirmation"
                           class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 py-3 pl-11 pr-4 text-sm outline-none focus:border-luxor-accent">
                </div>
            </div>
        </div>

        <div class="flex justify-end border-t p-5" style="border-color: var(--lx-border);">
            <button type="submit"
                    class="btn-luxor inline-flex items-center gap-2 rounded-full bg-luxor-accent px-6 py-2.5 text-sm font-semibold text-white hover:bg-luxor-accentDark">
                <i class="bi bi-check2-circle"></i> Guardar cambios
            </button>
        </div>
    </form>
</div>
@endsection