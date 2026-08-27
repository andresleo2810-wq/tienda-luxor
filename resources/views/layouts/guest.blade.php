<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Iniciar sesión - Tienda Luxor')</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <script>
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
        :root, [data-theme="cobalto"] {
            --lx-bg:#F3F6FB; --lx-surface:#FFFFFF; --lx-surface2:#E8EEF7;
            --lx-sidebar:#142D57; --lx-active:#214D91; --lx-accent:#2864D7; --lx-accentDark:#194BA9;
            --lx-text:#162A4A; --lx-muted:#687992; --lx-border:#DBE3EF;
            --lx-blue:#49A4D8; --lx-orange:#EF9B45; --lx-purple:#7A76C7; --lx-danger:#D65C68;
        }
        [data-theme="sage"] {
            --lx-bg:#F2F5F0; --lx-surface:#FBFDF9; --lx-surface2:#E8EEE7;
            --lx-sidebar:#29463B; --lx-active:#3B6852; --lx-accent:#6E9F78; --lx-accentDark:#4E7E5B;
            --lx-text:#24372F; --lx-muted:#708178; --lx-border:#DCE6DD;
            --lx-blue:#7097A7; --lx-orange:#C9924D; --lx-purple:#8E82A6; --lx-danger:#C76868;
        }
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

        .dot {
            width: 22px; height: 22px; border-radius: 9999px;
            border: 2px solid var(--lx-surface);
            box-shadow: 0 0 0 1px var(--lx-border), 0 2px 4px rgba(0,0,0,.15);
            cursor: pointer;
            transition: transform .15s ease;
            padding: 0;
        }
        .dot:hover { transform: scale(1.15); }
        .dot-active {
            outline: 2px solid var(--lx-accent);
            outline-offset: 3px;
            transform: scale(1.1);
        }
    </style>
</head>
<body class="min-h-screen">
    <div class="min-h-screen flex items-center justify-center p-4">
        @yield('content')
    </div>

    <script>
        let lastLight = localStorage.getItem('luxor-light') || 'cobalto';

        function applyTheme(t) {
            document.documentElement.setAttribute('data-theme', t);
            // Actualiza estado activo en los círculos de color
            ['cobalto', 'sage', 'dark'].forEach(x => {
                const el = document.getElementById('dot-' + x);
                if (el) el.classList.toggle('dot-active', x === t);
            });
            if (t !== 'dark') lastLight = t;
        }
        function setTheme(t) {
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
</body>
</html>