<form action="{{ $ruta }}" method="POST" class="rounded-xl border border-luxor-border bg-luxor-surface shadow-sm">
    @csrf
    @if($metodo !== 'POST') @method($metodo) @endif

    {{-- ============ SECCIÓN 1: DATOS PRINCIPALES ============ --}}
    <div class="border-b p-5" style="border-color: var(--lx-border);">
        <h2 class="font-semibold"><i class="bi bi-box-seam text-luxor-accent"></i> Datos principales</h2>
        <p class="mt-1 text-xs text-luxor-muted">Información básica del producto. Campos con * son obligatorios.</p>
    </div>

    <div class="grid gap-5 p-5 sm:grid-cols-2">
        {{-- Nombre --}}
        <div class="sm:col-span-2">
            <label class="mb-2 block text-sm font-medium">Nombre del producto *</label>
            <div class="relative">
                <i class="bi bi-tag absolute left-4 top-1/2 -translate-y-1/2 text-luxor-muted"></i>
                <input type="text" name="nombre_producto" required maxlength="100"
                       value="{{ old('nombre_producto', $producto->nombre_producto ?? '') }}"
                       class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 py-3 pl-11 pr-4 text-sm outline-none focus:border-luxor-accent"
                       placeholder="Ej: Whisky Black Label 750ml">
            </div>
            @error('nombre_producto') <p class="mt-1 text-xs text-luxor-danger">{{ $message }}</p> @enderror
        </div>

        {{-- Marca --}}
        <div>
            <label class="mb-2 block text-sm font-medium">Marca</label>
            <div class="relative">
                <i class="bi bi-award absolute left-4 top-1/2 -translate-y-1/2 text-luxor-muted"></i>
                <input type="text" name="marca" maxlength="100"
                       value="{{ old('marca', $producto->marca ?? '') }}"
                       class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 py-3 pl-11 pr-4 text-sm outline-none focus:border-luxor-accent"
                       placeholder="Ej: Johnnie Walker">
            </div>
            @error('marca') <p class="mt-1 text-xs text-luxor-danger">{{ $message }}</p> @enderror
        </div>

        {{-- Categoría --}}
        <div>
            <label class="mb-2 block text-sm font-medium">Categoría *</label>
            <div class="relative">
                <i class="bi bi-collection absolute left-4 top-1/2 -translate-y-1/2 text-luxor-muted"></i>
                <input type="text" name="categoria" required maxlength="50" list="categorias"
                       value="{{ old('categoria', $producto->categoria ?? '') }}"
                       class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 py-3 pl-11 pr-4 text-sm outline-none focus:border-luxor-accent"
                       placeholder="Ej: Whisky, Cerveza, Vino">
                <datalist id="categorias">
                    <option value="Whisky">
                    <option value="Ron">
                    <option value="Vodka">
                    <option value="Tequila">
                    <option value="Aguardiente">
                    <option value="Cerveza">
                    <option value="Vino">
                    <option value="Gin">
                    <option value="Brandy">
                    <option value="Licores">
                    <option value="Snacks">
                </datalist>
            </div>
            @error('categoria') <p class="mt-1 text-xs text-luxor-danger">{{ $message }}</p> @enderror
        </div>

        {{-- Código de barras --}}
        <div class="sm:col-span-2">
            <label class="mb-2 block text-sm font-medium">Código de barras</label>
            <div class="relative">
                <i class="bi bi-upc-scan absolute left-4 top-1/2 -translate-y-1/2 text-luxor-muted"></i>
                <input type="text" name="codigo_barras"
                       value="{{ old('codigo_barras', $producto->codigo_barras ?? '') }}"
                       class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 py-3 pl-11 pr-4 text-sm outline-none focus:border-luxor-accent"
                       placeholder="Ej: 7501234567890">
            </div>
            @error('codigo_barras') <p class="mt-1 text-xs text-luxor-danger">{{ $message }}</p> @enderror
        </div>

        {{-- Descripción --}}
        <div class="sm:col-span-2">
            <label class="mb-2 block text-sm font-medium">Descripción</label>
            <textarea name="descripcion" rows="2"
                      class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 px-4 py-3 text-sm outline-none focus:border-luxor-accent"
                      placeholder="Notas adicionales sobre el producto...">{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
            @error('descripcion') <p class="mt-1 text-xs text-luxor-danger">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- ============ SECCIÓN 2: PRECIOS Y STOCK ============ --}}
    <div class="border-t border-b p-5" style="border-color: var(--lx-border); background: var(--lx-surface2);">
        <h2 class="font-semibold"><i class="bi bi-cash-coin text-luxor-accent"></i> Precios y stock</h2>
    </div>

    <div class="grid gap-5 p-5 sm:grid-cols-2 lg:grid-cols-4">
        <div>
            <label class="mb-2 block text-sm font-medium">Precio costo *</label>
            <div class="relative">
                <i class="bi bi-cash absolute left-4 top-1/2 -translate-y-1/2 text-luxor-muted"></i>
                <input type="number" name="precio_costo" step="any" min="0" required
                       value="{{ old('precio_costo', $producto->precio_costo ?? '') }}"
                       class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 py-3 pl-11 pr-4 text-sm outline-none focus:border-luxor-accent"
                       placeholder="0">
            </div>
            @error('precio_costo') <p class="mt-1 text-xs text-luxor-danger">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium">Precio venta *</label>
            <div class="relative">
                <i class="bi bi-currency-dollar absolute left-4 top-1/2 -translate-y-1/2 text-luxor-muted"></i>
                <input type="number" name="precio_venta" step="any" min="0" required
                       value="{{ old('precio_venta', $producto->precio_venta ?? '') }}"
                       class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 py-3 pl-11 pr-4 text-sm outline-none focus:border-luxor-accent"
                       placeholder="0">
            </div>
            @error('precio_venta') <p class="mt-1 text-xs text-luxor-danger">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium">Stock actual *</label>
            <div class="relative">
                <i class="bi bi-boxes absolute left-4 top-1/2 -translate-y-1/2 text-luxor-muted"></i>
                <input type="number" name="stock_actual" min="0" required
                       value="{{ old('stock_actual', $producto->stock_actual ?? '') }}"
                       class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 py-3 pl-11 pr-4 text-sm outline-none focus:border-luxor-accent"
                       placeholder="0">
            </div>
            @error('stock_actual') <p class="mt-1 text-xs text-luxor-danger">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium">Stock mínimo</label>
            <div class="relative">
                <i class="bi bi-exclamation-triangle absolute left-4 top-1/2 -translate-y-1/2 text-luxor-muted"></i>
                <input type="number" name="stock_minimo" min="0"
                       value="{{ old('stock_minimo', $producto->stock_minimo ?? 5) }}"
                       class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 py-3 pl-11 pr-4 text-sm outline-none focus:border-luxor-accent"
                       placeholder="Ej: 5">
            </div>
            @error('stock_minimo') <p class="mt-1 text-xs text-luxor-danger">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- ============ SECCIÓN 3: CARACTERÍSTICAS DEL LICOR ============ --}}
    <div class="border-t border-b p-5" style="border-color: var(--lx-border); background: var(--lx-surface2);">
        <h2 class="font-semibold"><i class="bi bi-cup-hot text-luxor-accent"></i> Características del licor</h2>
        <p class="mt-1 text-xs text-luxor-muted">Datos técnicos específicos para bebidas alcohólicas.</p>
    </div>

    <div class="grid gap-5 p-5 sm:grid-cols-3">
        <div>
            <label class="mb-2 block text-sm font-medium">Grado alcohólico (%)</label>
            <div class="relative">
                <i class="bi bi-droplet-half absolute left-4 top-1/2 -translate-y-1/2 text-luxor-muted"></i>
                <input type="number" name="grado_alcoholico" step="0.1" min="0" max="100"
                       value="{{ old('grado_alcoholico', $producto->grado_alcoholico ?? '') }}"
                       class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 py-3 pl-11 pr-4 text-sm outline-none focus:border-luxor-accent"
                       placeholder="Ej: 40.0">
            </div>
            @error('grado_alcoholico') <p class="mt-1 text-xs text-luxor-danger">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium">Volumen (ml)</label>
            <div class="relative">
                <i class="bi bi-rulers absolute left-4 top-1/2 -translate-y-1/2 text-luxor-muted"></i>
                <input type="number" name="volumen_ml" min="0"
                       value="{{ old('volumen_ml', $producto->volumen_ml ?? '') }}"
                       class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 py-3 pl-11 pr-4 text-sm outline-none focus:border-luxor-accent"
                       placeholder="Ej: 750">
            </div>
            @error('volumen_ml') <p class="mt-1 text-xs text-luxor-danger">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium">País de origen</label>
            <div class="relative">
                <i class="bi bi-globe-americas absolute left-4 top-1/2 -translate-y-1/2 text-luxor-muted"></i>
                <input type="text" name="pais_origen" maxlength="50"
                       value="{{ old('pais_origen', $producto->pais_origen ?? '') }}"
                       class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 py-3 pl-11 pr-4 text-sm outline-none focus:border-luxor-accent"
                       placeholder="Ej: Escocia, Colombia">
            </div>
            @error('pais_origen') <p class="mt-1 text-xs text-luxor-danger">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- ============ SECCIÓN 4: FECHAS Y ESTADO ============ --}}
    <div class="border-t border-b p-5" style="border-color: var(--lx-border); background: var(--lx-surface2);">
        <h2 class="font-semibold"><i class="bi bi-calendar-check text-luxor-accent"></i> Vigencia y estado</h2>
    </div>

    <div class="grid gap-5 p-5 sm:grid-cols-2">
        {{-- Vencimiento --}}
        <div>
            <label class="mb-2 block text-sm font-medium">Fecha de vencimiento <span class="text-luxor-muted">(opcional)</span></label>
            <div class="relative">
                <i class="bi bi-calendar-event absolute left-4 top-1/2 -translate-y-1/2 text-luxor-muted"></i>
                <input type="date" name="fecha_vencimiento"
                       value="{{ old('fecha_vencimiento', optional($producto->fecha_vencimiento ?? null)->format('Y-m-d')) }}"
                       class="w-full rounded-lg border border-luxor-border bg-luxor-surface2 py-3 pl-11 pr-4 text-sm outline-none focus:border-luxor-accent">
            </div>
            <p class="mt-1 text-xs text-luxor-muted">
                <i class="bi bi-info-circle"></i> Déjala vacía en destilados (whisky, ron) que no vencen.
            </p>
            @error('fecha_vencimiento') <p class="mt-1 text-xs text-luxor-danger">{{ $message }}</p> @enderror
        </div>

        {{-- Estado --}}
        <div>
            <label class="mb-2 block text-sm font-medium">Estado</label>
            <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-luxor-border bg-luxor-surface2 px-4 py-3 hover:bg-luxor-border/50">
                <input type="checkbox" name="estado" value="1"
                       class="h-5 w-5 rounded border-luxor-border"
                       {{ old('estado', $producto->estado ?? true) ? 'checked' : '' }}>
                <div>
                    <strong class="block text-sm">Producto activo</strong>
                    <small class="text-xs text-luxor-muted">Aparece en ventas y reportes</small>
                </div>
            </label>
        </div>
    </div>

    {{-- ============ BOTONES ============ --}}
    <div class="flex justify-end gap-2 border-t p-5" style="border-color: var(--lx-border);">
        <a href="{{ route('productos.index') }}"
           class="rounded-full border border-luxor-border bg-luxor-surface2 px-5 py-2.5 text-sm font-semibold text-luxor-muted hover:text-luxor-text">
            Cancelar
        </a>
        <button type="submit"
                class="btn-luxor inline-flex items-center gap-2 rounded-full bg-luxor-accent px-6 py-2.5 text-sm font-semibold text-white hover:bg-luxor-accentDark">
            <i class="bi bi-check2-circle"></i> {{ $producto ? 'Guardar cambios' : 'Crear producto' }}
        </button>
    </div>
</form>