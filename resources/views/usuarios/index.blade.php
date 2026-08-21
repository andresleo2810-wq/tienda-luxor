@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
<div class="mb-2 text-xs text-luxor-muted">Gestión / Usuarios</div>
<div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-4xl font-semibold tracking-tight">Usuarios</h1>
        <p class="mt-2 text-sm text-luxor-muted">Administra el acceso del personal al sistema.</p>
    </div>
    <a href="{{ route('usuarios.create') }}"
       class="btn-luxor inline-flex items-center gap-2 rounded-full bg-luxor-accent px-5 py-2.5 text-sm font-semibold text-white hover:bg-luxor-accentDark">
        <i class="bi bi-person-plus"></i> Nuevo Usuario
    </a>
</div>

<div class="rounded-xl border border-luxor-border bg-luxor-surface shadow-sm">
    <div class="p-5">
        <div class="relative sm:max-w-xs">
            <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-sm text-luxor-muted"></i>
            <input id="buscarUsuario" type="text" placeholder="Buscar usuario..."
                   class="w-full rounded-full border border-luxor-border bg-luxor-surface2 py-2.5 pl-10 pr-4 text-sm outline-none focus:border-luxor-accent">
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-luxor-surface2 text-left text-xs text-luxor-muted">
                    <th class="px-5 py-3 font-semibold">Usuario</th>
                    <th class="px-5 py-3 font-semibold">Login</th>
                    <th class="px-5 py-3 font-semibold">Rol</th>
                    <th class="px-5 py-3 font-semibold">Estado</th>
                    <th class="px-5 py-3 font-semibold">Creado</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody id="tablaUsuarios">
                @forelse($usuarios as $usuario)
                <tr class="border-t border-luxor-border hover:bg-luxor-surface2/50">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-luxor-active text-xs font-bold text-white">
                                {{ strtoupper(substr($usuario->nombre_completo, 0, 1)) }}
                            </span>
                            <strong>{{ $usuario->nombre_completo }}</strong>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-luxor-muted">{{ $usuario->usuario_login }}</td>
                    <td class="px-5 py-4">
                        @if(optional($usuario->rol)->nombre_rol == 'Administrador')
                            <span class="rounded-full bg-purple-500/15 px-3 py-1 text-xs text-purple-500">Administrador</span>
                        @else
                            <span class="rounded-full bg-blue-500/15 px-3 py-1 text-xs text-blue-500">{{ optional($usuario->rol)->nombre_rol }}</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        @if($usuario->estado)
                            <span class="rounded-full bg-emerald-500/15 px-3 py-1 text-xs text-emerald-500">Activo</span>
                        @else
                            <span class="rounded-full bg-red-500/15 px-3 py-1 text-xs text-red-500">Inactivo</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-5 py-4 text-luxor-muted">{{ optional($usuario->created_at)->format('d/m/Y') }}</td>
                    <td class="px-5 py-4 text-right">
                        <div class="inline-flex gap-2">
                            <a href="{{ route('usuarios.edit', $usuario->id) }}" title="Editar"
                               class="grid h-8 w-8 place-items-center rounded-lg bg-luxor-surface2 text-luxor-muted transition hover:bg-luxor-accent hover:text-white">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST"
                                  onsubmit="return confirm('¿Eliminar al usuario {{ $usuario->nombre_completo }}?')">
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
                    <td colspan="6" class="px-5 py-12 text-center text-luxor-muted">
                        <i class="bi bi-people display-6"></i><br>No hay usuarios registrados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($usuarios, 'links'))
    <div class="p-4">{{ $usuarios->links() }}</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('buscarUsuario').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#tablaUsuarios tr').forEach(tr => {
            tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>
@endpush