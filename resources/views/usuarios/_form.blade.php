@php
    try { $roles = $roles ?? \App\Models\Rol::all(); } catch (\Throwable $e) { $roles = collect(); }
@endphp

<form action="{{ $ruta }}" method="POST" class="mx-auto max-w-2xl rounded-xl border border-luxor-border bg-luxor-surface shadow-sm">
    @csrf
    @if($metodo !== 'POST') @method($metodo) @endif

    <div class="border-b p-5" style="border-color: var(--lx-border);">
        <h2 class="font-semibold"><i class="bi bi-person-badge text-luxor-accent"></i> Datos del usuario</h2>
        <p class="mt-1 text-xs text-luxor-muted">Campos con * son obligatorios.</p>
    </div>

    <div class="grid gap-5 p-5 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label class="mb-2 block text-sm font-medium">Nombre completo *</label>
            <div class="relative">
                <i class="bi bi-person absolute left-4 top-1/2 -translate-y-1/2 text-luxor-muted"></i>
                <input type="text" name="nombre_completo" required
                       value="{{ old('nombre_completo', $usuario->nombre_completo ?? '') }}"
                       class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 py-3 pl-11 pr-4 text-sm outline-none focus:border-luxor-accent"
                       placeholder="Ej: María González">
            </div>
            @error('nombre_completo') <p class="mt-1 text-xs text-luxor-danger">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium">Usuario de acceso *</label>
            <div class="relative">
                <i class="bi bi-at absolute left-4 top-1/2 -translate-y-1/2 text-luxor-muted"></i>
                <input type="text" name="usuario_login" required
                       value="{{ old('usuario_login', $usuario->usuario_login ?? '') }}"
                       class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 py-3 pl-11 pr-4 text-sm outline-none focus:border-luxor-accent"
                       placeholder="Ej: mgonzalez">
            </div>
            @error('usuario_login') <p class="mt-1 text-xs text-luxor-danger">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium">
                Contraseña {{ $usuario ? '(vacía = no cambiar)' : '*' }}
            </label>
            <div class="relative">
                <i class="bi bi-lock absolute left-4 top-1/2 -translate-y-1/2 text-luxor-muted"></i>
                <input type="password" name="password" {{ $usuario ? '' : 'required' }}
                       class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 py-3 pl-11 pr-4 text-sm outline-none focus:border-luxor-accent"
                       placeholder="••••••••">
            </div>
            @error('password') <p class="mt-1 text-xs text-luxor-danger">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium">Rol *</label>
            <select name="id_rol" required
                    class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 px-4 py-3 text-sm outline-none focus:border-luxor-accent">
                <option value="">Seleccione...</option>
                @foreach($roles as $rol)
                <option value="{{ $rol->id }}" {{ old('id_rol', $usuario->id_rol ?? '') == $rol->id ? 'selected' : '' }}>
                    {{ $rol->nombre_rol }}
                </option>
                @endforeach
            </select>
            @error('id_rol') <p class="mt-1 text-xs text-luxor-danger">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium">Estado</label>
            <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-luxor-border bg-luxor-surface2 px-4 py-3">
                <input type="checkbox" name="estado" value="1" class="h-5 w-5"
                       {{ old('estado', $usuario->estado ?? true) ? 'checked' : '' }}>
                <div>
                    <strong class="block text-sm">Usuario activo</strong>
                    <small class="text-xs text-luxor-muted">Puede iniciar sesión</small>
                </div>
            </label>
        </div>
    </div>

    <div class="flex justify-end gap-2 border-t p-5" style="border-color: var(--lx-border);">
        <a href="{{ route('usuarios.index') }}"
           class="rounded-full border border-luxor-border bg-luxor-surface2 px-5 py-2.5 text-sm font-semibold text-luxor-muted hover:text-luxor-text">
            Cancelar
        </a>
        <button type="submit"
                class="btn-luxor inline-flex items-center gap-2 rounded-full bg-luxor-accent px-6 py-2.5 text-sm font-semibold text-white hover:bg-luxor-accentDark">
            <i class="bi bi-check2-circle"></i> {{ $usuario ? 'Guardar cambios' : 'Crear usuario' }}
        </button>
    </div>
</form>
