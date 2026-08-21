<form action="{{ $ruta }}" method="POST" class="mx-auto max-w-2xl rounded-xl border border-luxor-border bg-luxor-surface shadow-sm">
    @csrf
    @if($metodo !== 'POST') @method($metodo) @endif

    <div class="border-b p-5" style="border-color: var(--lx-border);">
        <h2 class="font-semibold"><i class="bi bi-truck text-luxor-accent"></i> Datos del proveedor</h2>
    </div>

    <div class="grid gap-5 p-5 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label class="mb-2 block text-sm font-medium">Nombre / Razón social *</label>
            <div class="relative">
                <i class="bi bi-building absolute left-4 top-1/2 -translate-y-1/2 text-luxor-muted"></i>
                <input type="text" name="nombre" required
                       value="{{ old('nombre', $proveedor->nombre ?? '') }}"
                       class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 py-3 pl-11 pr-4 text-sm outline-none focus:border-luxor-accent"
                       placeholder="Ej: Distribuidora de Licores S.A.S">
            </div>
            @error('nombre') <p class="mt-1 text-xs text-luxor-danger">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium">NIT</label>
            <div class="relative">
                <i class="bi bi-hash absolute left-4 top-1/2 -translate-y-1/2 text-luxor-muted"></i>
                <input type="text" name="nit" value="{{ old('nit', $proveedor->nit ?? '') }}"
                       class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 py-3 pl-11 pr-4 text-sm outline-none focus:border-luxor-accent"
                       placeholder="900.123.456-7">
            </div>
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium">Teléfono</label>
            <div class="relative">
                <i class="bi bi-telephone absolute left-4 top-1/2 -translate-y-1/2 text-luxor-muted"></i>
                <input type="text" name="telefono" value="{{ old('telefono', $proveedor->telefono ?? '') }}"
                       class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 py-3 pl-11 pr-4 text-sm outline-none focus:border-luxor-accent"
                       placeholder="604 123 4567">
            </div>
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium">Email</label>
            <div class="relative">
                <i class="bi bi-envelope absolute left-4 top-1/2 -translate-y-1/2 text-luxor-muted"></i>
                <input type="email" name="email" value="{{ old('email', $proveedor->email ?? '') }}"
                       class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 py-3 pl-11 pr-4 text-sm outline-none focus:border-luxor-accent"
                       placeholder="ventas@proveedor.com">
            </div>
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium">Dirección</label>
            <div class="relative">
                <i class="bi bi-geo-alt absolute left-4 top-1/2 -translate-y-1/2 text-luxor-muted"></i>
                <input type="text" name="direccion" value="{{ old('direccion', $proveedor->direccion ?? '') }}"
                       class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 py-3 pl-11 pr-4 text-sm outline-none focus:border-luxor-accent"
                       placeholder="Cra 45 # 12-34">
            </div>
        </div>

        <div class="sm:col-span-2">
            <label class="mb-2 block text-sm font-medium">Estado</label>
            <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-luxor-border bg-luxor-surface2 px-4 py-3">
                <input type="checkbox" name="estado" value="1" class="h-5 w-5"
                       {{ old('estado', $proveedor->estado ?? true) ? 'checked' : '' }}>
                <div>
                    <strong class="block text-sm">Proveedor activo</strong>
                    <small class="text-xs text-luxor-muted">Disponible para nuevos pedidos</small>
                </div>
            </label>
        </div>
    </div>

    <div class="flex justify-end gap-2 border-t p-5" style="border-color: var(--lx-border);">
        <a href="{{ route('proveedores.index') }}"
           class="rounded-full border border-luxor-border bg-luxor-surface2 px-5 py-2.5 text-sm font-semibold text-luxor-muted hover:text-luxor-text">
            Cancelar
        </a>
        <button type="submit"
                class="btn-luxor inline-flex items-center gap-2 rounded-full bg-luxor-accent px-6 py-2.5 text-sm font-semibold text-white hover:bg-luxor-accentDark">
            <i class="bi bi-check2-circle"></i> {{ $proveedor ? 'Guardar cambios' : 'Crear proveedor' }}
        </button>
    </div>
</form>