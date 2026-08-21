<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tienda Luxor')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <script>
        // Aplica el tema guardado ANTES de pintar (evita parpadeo)
        document.documentElement.setAttribute('data-theme', localStorage.getItem('luxor-theme') || 'cobalto');
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            corePlugins: { preflight: false },
            theme: {
                extend: {
                    colors: {
                        luxor: {
                            bg: 'var(--lx-bg)', surface: 'var(--lx-surface)', surface2: 'var(--lx-surface2)',
                            sidebar: 'var(--lx-sidebar)', active: 'var(--lx-active)',
                            accent: 'var(--lx-accent)', accentDark: 'var(--lx-accentDark)',
                            text: 'var(--lx-text)', muted: 'var(--lx-muted)', border: 'var(--lx-border)',
                            blue: 'var(--lx-blue)', orange: 'var(--lx-orange)',
                            purple: 'var(--lx-purple)', danger: 'var(--lx-danger)'
                        }
                    }
                }
            }
        }
    </script>

    <style>
                /* Oculta la barra de scroll fea del menú */
        .nav-scroll { scrollbar-width: none; -ms-overflow-style: none; }
        .nav-scroll::-webkit-scrollbar { display: none; }

        /* Iconos del menú más grandes y alineados */
        .nav-item-luxor i { font-size: 1.05rem; width: 1.3rem; text-align: center; }
                .nav-section {
            font-size: 10px; letter-spacing: .14em; text-transform: uppercase;
            color: rgba(148,163,184,.55); padding: 0 .75rem; margin: 1.1rem 0 .4rem;
        }
        .nav-item-luxor { position: relative; transition: all .2s ease; }
        .nav-item-luxor i { transition: transform .2s ease; }
        .nav-item-luxor:hover i { transform: translateX(3px) scale(1.1); }
        .nav-item-luxor.active {
            background: linear-gradient(90deg, var(--lx-active), rgba(0,0,0,0));
            color: #fff;
        }
        .nav-item-luxor.active::before {
            content: ''; position: absolute; left: -16px; top: 22%; bottom: 22%;
            width: 3px; border-radius: 9999px; background: var(--lx-accent);
        }
        .luxor-ai-card { position: relative; overflow: hidden; }
        .luxor-ai-card::after {
            content: ''; position: absolute; inset: 0; pointer-events: none;
            background: radial-gradient(circle at 15% 20%, rgba(37,181,142,.28), transparent 60%);
            animation: pulseGlow 3s ease-in-out infinite;
        }
        @keyframes pulseGlow { 0%,100% { opacity: .45; } 50% { opacity: 1; } }
        /* ============ TEMA COBALTO (por defecto) ============ */
        :root, [data-theme="cobalto"] {
            --lx-bg:#F3F6FB; --lx-surface:#FFFFFF; --lx-surface2:#E8EEF7;
            --lx-sidebar:#142D57; --lx-active:#214D91; --lx-accent:#2864D7; --lx-accentDark:#194BA9;
            --lx-text:#162A4A; --lx-muted:#687992; --lx-border:#DBE3EF;
            --lx-blue:#49A4D8; --lx-orange:#EF9B45; --lx-purple:#7A76C7; --lx-danger:#D65C68;
        }
        /* ============ TEMA SAGE ============ */
        [data-theme="sage"] {
            --lx-bg:#F2F5F0; --lx-surface:#FBFDF9; --lx-surface2:#E8EEE7;
            --lx-sidebar:#29463B; --lx-active:#3B6852; --lx-accent:#6E9F78; --lx-accentDark:#4E7E5B;
            --lx-text:#24372F; --lx-muted:#708178; --lx-border:#DCE6DD;
            --lx-blue:#7097A7; --lx-orange:#C9924D; --lx-purple:#8E82A6; --lx-danger:#C76868;
        }
        /* ============ TEMA OSCURO ============ */
        [data-theme="dark"] {
            --lx-bg:#111B23; --lx-surface:#182630; --lx-surface2:#22333E;
            --lx-sidebar:#0B151D; --lx-active:#164E4B; --lx-accent:#18A889; --lx-accentDark:#087D69;
            --lx-text:#EDF3F5; --lx-muted:#94A6B2; --lx-border:#2B3B46;
            --lx-blue:#4E88F5; --lx-orange:#E79A3A; --lx-purple:#9B7BE5; --lx-danger:#DF6262;
        }
        body { background: var(--lx-bg); color: var(--lx-text); }

        /* Elimina el borde gris "anticuado" del navegador en todos los botones */
        button {
            border: 0;
            background: transparent;
            font: inherit;
            color: inherit;
            cursor: pointer;
            padding: 0;
        }

        /* Micro-animación premium para botones principales */
        .btn-luxor {
            box-shadow: 0 1px 2px rgba(0,0,0,.1);
            transition: all .15s ease;
        }
        .btn-luxor:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(0,0,0,.18);
        }
        .btn-luxor:active { transform: translateY(0); }
        .sidebar { width: 248px; }
        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); transition: transform .25s ease; }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0 !important; }
        }
        @media print { aside, header.nav-luxor { display:none !important; } .main-content { margin-left:0 !important; } }
    </style>
</head>
<body class="min-h-screen">

@auth
@php
    $stockBajoLayout = \App\Models\Producto::where('estado', true)
        ->whereColumn('stock_actual', '<=', 'stock_minimo')->count();
    $nombres = explode(' ', Auth::user()->nombre_completo);
    $iniciales = strtoupper(substr($nombres[0] ?? 'U', 0, 1) . substr($nombres[1] ?? '', 0, 1));
    $esAdmin = Auth::user()->rol->nombre_rol == 'Administrador';
        $pedidosPendLayout = \App\Models\Pedido::where('estado', 'Pendiente')->count();
    $cajasAbiertasLayout = \App\Models\Caja::where('estado', 'Abierta')->count();
@endphp

<div class="min-h-screen">
    <!-- ============ SIDEBAR ============ -->
            <aside id="sidebar" class="sidebar fixed inset-y-0 left-0 z-50 flex flex-col bg-luxor-sidebar px-4 py-6">
        <div class="mb-6 flex items-center gap-3 px-2">
            <div class="grid h-10 w-10 place-items-center rounded-full bg-luxor-accent text-lg font-bold text-white shadow-lg">L</div>
            <div>
                <strong class="block text-sm text-white">Tienda Luxor</strong>
                <small class="text-[10px] text-slate-400">Gestión inteligente</small>
            </div>
            <button onclick="toggleSidebar()" class="ml-auto text-slate-400 lg:hidden">✕</button>
        </div>

        {{-- Chip informativo (ya no parece botón) --}}
        <div class="mb-2 flex items-center gap-2 rounded-lg border border-white/10 px-3 py-3 text-xs text-slate-300">
            <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
            <span>Sede Principal</span>
            <span class="ml-auto text-[10px] text-slate-500">Activa</span>
        </div>

        <nav class="nav-scroll flex-1 overflow-y-auto pr-1">
            <p class="nav-section">Principal</p>
            <a href="{{ route('dashboard') }}" class="nav-item-luxor flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm {{ request()->routeIs('dashboard') ? 'active' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <i class="bi bi-speedometer2"></i><span>Dashboard</span>
            </a>

            <p class="nav-section">Operación</p>
            <a href="{{ route('productos.index') }}" class="nav-item-luxor flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm {{ request()->routeIs('productos.*') ? 'active' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <i class="bi bi-box-seam"></i><span>Productos</span>
                @if($stockBajoLayout > 0)
                <span class="ml-auto rounded-full bg-amber-500/20 px-2 py-0.5 text-[10px] font-bold text-amber-300">{{ $stockBajoLayout }}</span>
                @endif
            </a>
            <a href="{{ route('ventas.index') }}" class="nav-item-luxor flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm {{ request()->routeIs('ventas.*') ? 'active' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <i class="bi bi-bag-check"></i><span>Ventas</span>
            </a>
            @if($esAdmin)
            <a href="{{ route('pedidos.index') }}" class="nav-item-luxor flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm {{ request()->routeIs('pedidos.*') ? 'active' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <i class="bi bi-box-arrow-in-down"></i><span>Pedidos</span>
                @if($pedidosPendLayout > 0)
                <span class="ml-auto rounded-full bg-blue-500/20 px-2 py-0.5 text-[10px] font-bold text-blue-300">{{ $pedidosPendLayout }}</span>
                @endif
            </a>
            @endif
            <a href="{{ route('cajas.index') }}" class="nav-item-luxor flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm {{ request()->routeIs('cajas.*') ? 'active' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <i class="bi bi-cash-stack"></i><span>Caja</span>
                @if($cajasAbiertasLayout > 0)
                <span class="ml-auto rounded-full bg-emerald-500/20 px-2 py-0.5 text-[10px] font-bold text-emerald-300">{{ $cajasAbiertasLayout }}</span>
                @endif
            </a>

            @if($esAdmin)
            <p class="nav-section">Administración</p>
            <a href="{{ route('usuarios.index') }}" class="nav-item-luxor flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm {{ request()->routeIs('usuarios.*') ? 'active' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <i class="bi bi-people"></i><span>Usuarios</span>
            </a>
            <a href="{{ route('proveedores.index') }}" class="nav-item-luxor flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm {{ request()->routeIs('proveedores.*') ? 'active' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <i class="bi bi-truck"></i><span>Proveedores</span>
            </a>
            <a href="{{ route('reportes.ventas') }}" class="nav-item-luxor flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm {{ request()->routeIs('reportes.*') ? 'active' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <i class="bi bi-graph-up-arrow"></i><span>Reportes</span>
            </a>
            <a href="{{ route('auditoria.index') }}" class="nav-item-luxor flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm {{ request()->routeIs('auditoria.*') ? 'active' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <i class="bi bi-journal-text"></i><span>Auditoría</span>
            </a>
            @endif

            <p class="nav-section">Sistema</p>
            <a href="{{ route('perfil') }}" class="nav-item-luxor flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm {{ request()->routeIs('perfil*') ? 'active' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <i class="bi bi-person-circle"></i><span>Mi Perfil</span>
            </a>
        </nav>

        <div class="mt-4 space-y-3">
            {{-- Luxor AI: ahora con accesos directos reales --}}
            <div class="dropdown">
                <button type="button" data-bs-toggle="dropdown"
                        class="luxor-ai-card flex w-full items-center gap-3 rounded-lg border border-emerald-500/30 bg-emerald-900/40 px-3 py-3 text-left">
                    <i class="bi bi-stars text-emerald-400"></i>
                    <span class="flex-1">
                        <strong class="block text-xs text-white">Luxor AI</strong>
                        <small class="text-[10px] text-slate-400">Tu asistente de negocio</small>
                    </span>
                    <i class="bi bi-chevron-down text-xs text-slate-400"></i>
                </button>
                <ul class="dropdown-menu w-100">
                    <li><a class="dropdown-item" href="{{ route('ventas.index') }}"><i class="bi bi-mic-fill me-2 text-danger"></i>Venta por voz</a></li>
                    <li><a class="dropdown-item" href="{{ route('pedidos.index') }}"><i class="bi bi-cpu me-2 text-primary"></i>Análisis de facturas</a></li>
                </ul>
            </div>

            {{-- Tarjeta de usuario con cierre de sesión --}}
            <div class="flex items-center gap-3 rounded-lg border border-white/10 bg-white/5 px-3 py-3">
                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-luxor-active text-xs font-bold text-white">{{ $iniciales }}</span>
                <div class="min-w-0 flex-1">
                    <strong class="block truncate text-xs text-white">{{ Auth::user()->nombre_completo }}</strong>
                    <small class="text-[10px] text-slate-400">{{ Auth::user()->rol->nombre_rol }}</small>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-slate-400 hover:text-red-400" title="Cerrar sesión"><i class="bi bi-box-arrow-right"></i></button>
                </form>
            </div>
        </div>
    </aside>

    <!-- ============ CONTENIDO ============ -->
    <div class="main-content ml-[248px] min-h-screen">
        <header class="nav-luxor sticky top-0 z-30 flex h-[74px] items-center justify-between border-b border-luxor-border bg-luxor-surface px-5 lg:px-10">
            <div class="flex items-center gap-3 text-xs">
                <button onclick="toggleSidebar()" class="text-xl text-luxor-muted lg:hidden">☰</button>
                <span class="text-luxor-muted">Gestión</span>
                <span class="text-luxor-muted">›</span>
                <strong>@yield('title', 'Dashboard')</strong>
            </div>

            <div class="flex items-center gap-3">
                {{-- Selector de tema --}}
                <div class="hidden items-center rounded-full border border-luxor-border bg-luxor-surface2 p-1 text-xs md:flex">
                    <button id="pill-cobalto" onclick="setTheme('cobalto')" class="rounded-full px-3 py-1">Cobalto</button>
                    <button id="pill-sage" onclick="setTheme('sage')" class="rounded-full px-3 py-1">Sage</button>
                </div>
                <button id="btn-dark" onclick="toggleDark()" class="text-luxor-muted hover:text-luxor-text" title="Modo oscuro">
                    <i class="bi bi-moon"></i>
                </button>
                <a href="{{ $esAdmin ? route('auditoria.index') : '#' }}" class="relative text-luxor-muted hover:text-luxor-text" title="Notificaciones">
                    <i class="bi bi-bell"></i>
                    @if($stockBajoLayout > 0)
                    <span class="absolute -right-0.5 -top-0.5 h-1.5 w-1.5 rounded-full bg-luxor-danger"></span>
                    @endif
                </a>

                <div class="dropdown">
                    <button class="flex items-center gap-2 border-0 bg-transparent" data-bs-toggle="dropdown">
                        <span class="grid h-8 w-8 place-items-center rounded-full bg-luxor-active text-xs font-bold text-white">{{ $iniciales }}</span>
                        <span class="hidden text-left sm:block">
                            <strong class="block text-xs">{{ Auth::user()->nombre_completo }}</strong>
                            <span class="text-[10px] text-luxor-muted">{{ Auth::user()->rol->nombre_rol }}</span>
                        </span>
                        <i class="bi bi-chevron-down text-xs text-luxor-muted"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-[1410px] px-4 py-8 sm:px-8">
            @if(session('success'))
            <div class="mb-4 rounded-lg border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="mb-4 rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
            </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>

@else
<div class="container py-5">@yield('content')</div>
@endauth

<script>
    function toggleSidebar() { document.getElementById('sidebar').classList.toggle('open'); }

    let lastLight = localStorage.getItem('luxor-light') || 'cobalto';

    function applyTheme(t) {
        document.documentElement.setAttribute('data-theme', t);
        const light = (t === 'dark') ? lastLight : t;
        const pc = document.getElementById('pill-cobalto');
        const ps = document.getElementById('pill-sage');
        if (pc && ps) {
            pc.className = 'rounded-full px-3 py-1 ' + (light === 'cobalto' ? 'bg-luxor-surface shadow text-luxor-text' : 'text-luxor-muted');
            ps.className = 'rounded-full px-3 py-1 ' + (light === 'sage' ? 'bg-luxor-surface shadow text-luxor-text' : 'text-luxor-muted');
        }
        const moon = document.getElementById('btn-dark');
        if (moon) moon.innerHTML = (t === 'dark') ? '<i class="bi bi-sun"></i>' : '<i class="bi bi-moon"></i>';
    }
    function setTheme(t) {
        lastLight = t;
        localStorage.setItem('luxor-light', t);
        localStorage.setItem('luxor-theme', t);
        applyTheme(t);
    }
    function toggleDark() {
        const cur = document.documentElement.getAttribute('data-theme');
        const next = (cur === 'dark') ? lastLight : 'dark';
        localStorage.setItem('luxor-theme', next);
        applyTheme(next);
    }
    applyTheme(document.documentElement.getAttribute('data-theme') || 'cobalto');
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
{{-- ============ MODAL GLOBAL DE CONFIRMACIÓN / AVISO ============ --}}
<div class="modal fade" id="modalLuxor" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow-2xl" style="background: var(--lx-surface); color: var(--lx-text); border-radius: 1.25rem;">
            <div class="p-5 text-center">
                <span id="luxorIcono" class="mx-auto grid h-14 w-14 place-items-center rounded-full bg-red-500/15 text-2xl text-red-500">
                    <i class="bi bi-exclamation-triangle"></i>
                </span>
                <h5 id="luxorTitulo" class="mt-3 font-semibold">Confirma la acción</h5>
                <p id="luxorMensaje" class="mt-2 text-sm text-luxor-muted"></p>
                <div class="mt-5 flex gap-2">
                    <button type="button" id="luxorCancel" data-bs-dismiss="modal"
                            class="w-full rounded-lg border px-4 py-2.5 text-sm font-semibold text-luxor-muted transition hover:text-luxor-text"
                            style="border-color: var(--lx-border);">
                        Cancelar
                    </button>
                    <button type="button" id="luxorOk"
                            class="btn-luxor w-full rounded-lg bg-luxor-danger px-4 py-2.5 text-sm font-semibold text-white">
                        Sí, continuar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let luxorModal = null;
    let luxorOnOk = null;

    function luxorDialogo(opciones) {
        luxorModal = luxorModal || new bootstrap.Modal(document.getElementById('modalLuxor'));

        document.getElementById('luxorTitulo').textContent = opciones.titulo || 'Aviso';
        document.getElementById('luxorMensaje').textContent = opciones.mensaje || '';

        const ok = document.getElementById('luxorOk');
        const cancel = document.getElementById('luxorCancel');
        const icono = document.getElementById('luxorIcono');

        ok.textContent = opciones.textoOk || 'Aceptar';
        cancel.style.display = opciones.esAlerta ? 'none' : '';

        const tipos = {
            danger:  { bg: 'bg-red-500/15',     tx: 'text-red-500',     ic: 'bi-exclamation-triangle', btn: 'bg-luxor-danger' },
            success: { bg: 'bg-emerald-500/15', tx: 'text-emerald-500', ic: 'bi-check-circle',         btn: 'bg-luxor-accent' },
            info:    { bg: 'bg-blue-500/15',    tx: 'text-blue-500',    ic: 'bi-info-circle',          btn: 'bg-luxor-accent' },
            warning: { bg: 'bg-amber-500/15',   tx: 'text-amber-500',   ic: 'bi-exclamation-triangle', btn: 'bg-luxor-accent' }
        };
        const t = tipos[opciones.tipo] || tipos.danger;
        icono.className = 'mx-auto grid h-14 w-14 place-items-center rounded-full text-2xl ' + t.bg + ' ' + t.tx;
        icono.innerHTML = '<i class="bi ' + t.ic + '"></i>';
        ok.className = 'btn-luxor w-full rounded-lg px-4 py-2.5 text-sm font-semibold text-white ' + t.btn;

        luxorOnOk = opciones.onOk || null;
        luxorModal.show();
    }

    document.getElementById('luxorOk').addEventListener('click', function () {
        luxorModal.hide();
        if (typeof luxorOnOk === 'function') luxorOnOk();
    });

    //  Intercepta TODOS los confirm() nativos del sistema
    window.confirm = function (mensaje) {
        const activo = document.activeElement;
        const form = activo && (activo.form || activo.closest('form'));
        luxorDialogo({
            titulo: 'Confirma la acción',
            mensaje: mensaje,
            tipo: 'danger',
            textoOk: 'Sí, continuar',
            onOk: function () { if (form) form.submit(); }
        });
        return false;
    };

    // 🎯 Intercepta TODOS los alert() nativos del sistema
    window.alert = function (mensaje) {
        luxorDialogo({
            titulo: 'Aviso',
            mensaje: mensaje,
            tipo: 'info',
            textoOk: 'Entendido',
            esAlerta: true
        });
    };
</script>
@stack('scripts')
</body>
</html>