@extends('layouts.app')

@section('title', 'Auditoría')

@section('content')
@php
    // Funciona con $registros, $auditorias o $logs (según tu controlador)
    $registros = $registros ?? $auditorias ?? $logs ?? collect();

    // KPIs calculados directo del modelo (independientes del controlador)
    $kpiHoy = \App\Models\AuditoriaLog::whereDate('created_at', now()->toDateString())->count();
    $kpiCriticas = \App\Models\AuditoriaLog::whereIn('accion', ['Anular', 'Eliminar', 'Cancelar'])->count();
    $kpiVozIA = \App\Models\AuditoriaLog::where('descripcion', 'LIKE', '%VOZ%')
        ->orWhere('descripcion', 'LIKE', '%IA%')->count();
@endphp

<div class="mb-2 text-xs text-luxor-muted">Gestión / Auditoría</div>
<div class="mb-8">
    <h1 class="text-4xl font-semibold tracking-tight">Auditoría</h1>
    <p class="mt-2 text-sm text-luxor-muted">Historial completo de acciones críticas del sistema.</p>
</div>

{{-- KPIs --}}
<div class="mb-5 grid gap-4 sm:grid-cols-3">
    <div class="rounded-xl border border-luxor-border bg-luxor-surface p-5 shadow-sm">
        <div class="flex items-center justify-between text-xs text-luxor-muted">
            <span>Registros de hoy</span>
            <span class="grid h-9 w-9 place-items-center rounded-lg bg-blue-500/15 text-blue-500"><i class="bi bi-calendar-check"></i></span>
        </div>
        <strong class="mt-3 block text-2xl">{{ $kpiHoy }}</strong>
    </div>
    <div class="rounded-xl border border-luxor-border bg-luxor-surface p-5 shadow-sm">
        <div class="flex items-center justify-between text-xs text-luxor-muted">
            <span>Acciones críticas</span>
            <span class="grid h-9 w-9 place-items-center rounded-lg bg-red-500/15 text-red-500"><i class="bi bi-shield-exclamation"></i></span>
        </div>
        <strong class="mt-3 block text-2xl">{{ $kpiCriticas }}</strong>
        <small class="text-xs text-luxor-muted">Anulaciones y eliminaciones</small>
    </div>
    <div class="rounded-xl border border-luxor-border bg-luxor-surface p-5 shadow-sm">
        <div class="flex items-center justify-between text-xs text-luxor-muted">
            <span>Operaciones con Voz / IA</span>
            <span class="grid h-9 w-9 place-items-center rounded-lg bg-emerald-500/15 text-emerald-500"><i class="bi bi-cpu"></i></span>
        </div>
        <strong class="mt-3 block text-2xl">{{ $kpiVozIA }}</strong>
        <small class="text-xs text-luxor-muted">Ventas por voz y análisis OCR</small>
    </div>
</div>

{{-- Tabla --}}
<div class="rounded-xl border border-luxor-border bg-luxor-surface shadow-sm">
    <div class="p-5">
        <div class="relative sm:max-w-xs">
            <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-sm text-luxor-muted"></i>
            <input id="buscarAuditoria" type="text" placeholder="Buscar usuario, acción, módulo..."
                   class="w-full rounded-full border border-luxor-border bg-luxor-surface2 py-2.5 pl-10 pr-4 text-sm outline-none focus:border-luxor-accent">
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-luxor-surface2 text-left text-xs text-luxor-muted">
                    <th class="px-5 py-3 font-semibold">Fecha y hora</th>
                    <th class="px-5 py-3 font-semibold">Usuario</th>
                    <th class="px-5 py-3 font-semibold">Acción</th>
                    <th class="px-5 py-3 font-semibold">Módulo</th>
                    <th class="px-5 py-3 font-semibold">Descripción</th>
                    <th class="px-5 py-3 font-semibold">IP</th>
                </tr>
            </thead>
            <tbody id="tablaAuditoria">
                @forelse($registros as $r)
                @php
                    $colorAccion = match (true) {
                        in_array($r->accion, ['Anular', 'Eliminar', 'Cancelar']) => 'bg-red-500/15 text-red-500',
                        $r->accion === 'Editar' => 'bg-blue-500/15 text-blue-500',
                        $r->accion === 'Crear' => 'bg-emerald-500/15 text-emerald-500',
                        in_array($r->accion, ['Login', 'Logout']) => 'bg-purple-500/15 text-purple-500',
                        $r->accion === 'Recibir' => 'bg-emerald-500/15 text-emerald-500',
                        default => 'bg-amber-500/15 text-amber-500',
                    };
                    $iconoModulo = match ($r->modulo) {
                        'Ventas' => 'bi-cart3',
                        'Productos' => 'bi-box-seam',
                        'Pedidos' => 'bi-box-arrow-in-down',
                        'Caja' => 'bi-wallet2',
                        'Usuarios' => 'bi-people',
                        'Proveedores' => 'bi-truck',
                        'Autenticación' => 'bi-shield-lock',
                        default => 'bi-journal-text',
                    };
                @endphp
                <tr class="border-t border-luxor-border hover:bg-luxor-surface2/50">
                    <td class="whitespace-nowrap px-5 py-4 text-luxor-muted">
                        {{ optional($r->created_at)->format('d/m/Y') }}
                        <strong class="block text-xs">{{ optional($r->created_at)->format('H:i:s') }}</strong>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-luxor-surface2 text-xs font-bold text-luxor-muted">
                                {{ strtoupper(substr($r->usuario_nombre ?? 'S', 0, 1)) }}
                            </span>
                            <strong>{{ $r->usuario_nombre }}</strong>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <span class="whitespace-nowrap rounded-full {{ $colorAccion }} px-3 py-1 text-xs">{{ $r->accion }}</span>
                    </td>
                    <td class="px-5 py-4">
                        <span class="flex items-center gap-2 text-luxor-muted">
                            <i class="bi {{ $iconoModulo }}"></i> {{ $r->modulo }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <span class="text-luxor-muted">
                            @if(str_contains($r->descripcion ?? '', 'VOZ'))
                                <i class="bi bi-mic-fill text-luxor-danger"></i>
                            @elseif(str_contains($r->descripcion ?? '', 'IA'))
                                <i class="bi bi-cpu text-luxor-accent"></i>
                            @endif
                            {{ $r->descripcion }}
                        </span>
                    </td>
                    <td class="whitespace-nowrap px-5 py-4 text-xs text-luxor-muted">{{ $r->ip_address }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center text-luxor-muted">
                        <i class="bi bi-journal-x display-6"></i><br>No hay registros de auditoría
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($registros, 'links'))
    <div class="p-4">{{ $registros->links() }}</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('buscarAuditoria').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#tablaAuditoria tr').forEach(tr => {
            tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>
@endpush