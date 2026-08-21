@extends('layouts.app')

@section('title', 'Proveedores')

@section('content')
<div class="mb-2 text-xs text-luxor-muted">Gestión / Proveedores</div>
<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-4xl font-semibold tracking-tight">Proveedores</h1>
        <p class="mt-2 text-sm text-luxor-muted">Directorio de distribuidores de la licorería.</p>
    </div>
    <a href="{{ route('proveedores.create') }}"
       class="btn-luxor inline-flex items-center gap-2 rounded-full bg-luxor-accent px-5 py-2.5 text-sm font-semibold text-white hover:bg-luxor-accentDark">
        <i class="bi bi-plus-lg"></i> Nuevo Proveedor
    </a>
</div>

<div class="rounded-xl border border-luxor-border bg-luxor-surface shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-luxor-surface2 text-left text-xs text-luxor-muted">
                    <th class="px-5 py-3 font-semibold">Proveedor</th>
                    <th class="px-5 py-3 font-semibold">Contacto</th>
                    <th class="px-5 py-3 font-semibold">Dirección</th>
                    <th class="px-5 py-3 font-semibold">Estado</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($proveedores as $proveedor)
                <tr class="border-t border-luxor-border hover:bg-luxor-surface2/50">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-purple-500/15 text-purple-500"><i class="bi bi-truck"></i></span>
                            <div>
                                <strong class="block">{{ $proveedor->nombre }}</strong>
                                <small class="text-xs text-luxor-muted">{{ $proveedor->nit ?? '' }}</small>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-luxor-muted">
                        @if($proveedor->telefono) <i class="bi bi-telephone"></i> {{ $proveedor->telefono }}<br> @endif
                        @if($proveedor->email) <i class="bi bi-envelope"></i> {{ $proveedor->email }} @endif
                    </td>
                    <td class="px-5 py-4 text-luxor-muted">{{ $proveedor->direccion ?? '—' }}</td>
                    <td class="px-5 py-4">
                        @if($proveedor->estado)
                            <span class="rounded-full bg-emerald-500/15 px-3 py-1 text-xs text-emerald-500">Activo</span>
                        @else
                            <span class="rounded-full bg-red-500/15 px-3 py-1 text-xs text-red-500">Inactivo</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-right">
                        <div class="inline-flex gap-2">
                            <a href="{{ route('proveedores.edit', $proveedor->id) }}" title="Editar"
                               class="grid h-8 w-8 place-items-center rounded-lg bg-luxor-surface2 text-luxor-muted transition hover:bg-luxor-accent hover:text-white">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('proveedores.destroy', $proveedor->id) }}" method="POST"
                                  onsubmit="return confirm('¿Eliminar al proveedor {{ $proveedor->nombre }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" title="Eliminar"
                                        class="grid h-8 w-8 place-items-center rounded-lg bg-luxor-surface2 text-luxor-muted transition hover:bg-luxor-danger hover:text-white">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-12 text-center text-luxor-muted">
                        <i class="bi bi-truck display-6"></i><br>No hay proveedores registrados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($proveedores, 'links'))
    <div class="p-4">{{ $proveedores->links() }}</div>
    @endif
</div>
@endsection