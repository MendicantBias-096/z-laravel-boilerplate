<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet"/>
    <tallstackui:script />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        [x-cloak] { display: none !important; }
        /* Iconos del toggle — reaccionan a html.dark via CSS, sin Alpine */
        .theme-icon-sun  { display: none;  }
        .theme-icon-moon { display: block; }
        html.dark .theme-icon-sun  { display: block; }
        html.dark .theme-icon-moon { display: none;  }
    </style>
    <script>
        (function () {
            var cookie = document.cookie.match(/darkMode=([^;]+)/);
            if (cookie) { var isDark = cookie[1] !== 'false'; }
            else {
                var stored = localStorage.getItem('darkMode');
                var isDark = stored !== null ? stored === 'true' : true;
            }
            document.documentElement.classList.toggle('dark', isDark);
            document.documentElement.style.backgroundColor = isDark ? '#080c18' : '#F7F8FA';
            if (!cookie) {
                document.cookie = 'darkMode=' + isDark + ';path=/;max-age=31536000;SameSite=Lax';
            }
        })();
    </script>
</head>
<body class="antialiased font-['Inter',sans-serif]" style="height: 100vh; overflow: hidden;">

{{-- Controles flotantes: tema + idioma --}}
<div class="fixed top-4 right-4 z-50 flex items-center gap-2">
    @include('public._partials.locale-switcher')
    <button type="button" onclick="toggleTheme()"
            class="inline-flex items-center justify-center w-9 h-9 rounded-xl
                   cursor-pointer transition-all duration-200"
            style="color: var(--ui-content-muted); background: var(--auth-link-bg); border: 1px solid var(--auth-link-border);"
            onmouseover="this.style.background='var(--auth-link-bg-hover)';this.style.color='var(--ui-content)';"
            onmouseout="this.style.background='var(--auth-link-bg)';this.style.color='var(--ui-content-muted)';"
            title="{{ __('public.toggle_theme') }}">
        <svg class="theme-icon-sun w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                  d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/>
        </svg>
        <svg class="theme-icon-moon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                  d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z"/>
        </svg>
    </button>
</div>

{{-- ════════════════════════════════════════════════════════════
     FONDO COMPLETO — base color + gradientes + dots + ondas
     position:fixed cubre siempre el viewport completo
════════════════════════════════════════════════════════════ --}}
<div class="fixed inset-0 pointer-events-none"
     style="background: var(--pub-base-bg); transition: background-color 300ms;">
    {{-- Gradientes de color --}}
    <div class="absolute inset-0"
         style="background:
                    radial-gradient(ellipse 80% 70% at 72% 50%, var(--pub-grad-a) 0%, transparent 60%),
                    radial-gradient(ellipse 55% 60% at 22% 50%, var(--pub-grad-b) 0%, transparent 55%);"></div>
    {{-- Dot pattern --}}
    <div class="absolute inset-0"
         style="background-image: radial-gradient(circle, var(--pub-dots) 1px, transparent 1px);
                background-size: 28px 28px;"></div>
    {{-- Canvas: ondas animadas a pantalla completa --}}
    <canvas id="auth-wave-canvas" class="absolute inset-0 w-full h-full" style="opacity: 0.9;"></canvas>
</div>

{{-- ════════════════════════════════════════════════════════════
     LAYOUT — dos paneles sobre el fondo
════════════════════════════════════════════════════════════ --}}
<div class="relative z-10 min-h-screen flex">

    {{-- ─── PANEL IZQUIERDO — Formulario ──────────────────── --}}
    <div class="flex-1 flex flex-col items-center justify-center px-8 lg:px-16 py-12 relative">

        {{-- Overlay desktop: degradado sólido → transparente hacia la derecha --}}
        <div class="absolute inset-0 hidden lg:block pointer-events-none"
             style="background: linear-gradient(to right,
                        var(--pub-base-bg) 0%,
                        var(--pub-base-bg) 42%,
                        var(--pub-base-bg) 55%,
                        transparent 100%);
                    transition: background-color 300ms;"></div>
        {{-- Overlay mobile: fondo sólido completo --}}
        <div class="absolute inset-0 lg:hidden pointer-events-none"
             style="background: var(--pub-base-bg); transition: background-color 300ms;"></div>

        {{-- Form slot --}}
        <div class="relative z-10 w-full max-w-sm">
            {{ $slot }}
        </div>

        {{-- Volver al inicio --}}
        <div class="relative z-10 w-full max-w-sm mt-10">
            <a href="{{ route('home') }}"
               class="inline-flex items-center gap-1.5 text-xs transition-colors duration-200"
               style="color: var(--ui-content-subtle);"
               onmouseover="this.style.color='var(--ui-content)';"
               onmouseout="this.style.color='var(--ui-content-subtle)';">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('public.back_home') }}
            </a>
        </div>
    </div>

    {{-- ─── PANEL DERECHO — Branding flotando sobre las ondas ── --}}
    <div class="hidden lg:flex flex-1 items-center justify-center pr-26">
        <div class="flex flex-col items-center text-center px-12">

            {{-- Logo grande --}}
            <div class="w-20 h-20 rounded-3xl flex items-center justify-center mb-7"
                 style="background: linear-gradient(135deg, #f53003 0%, #c0392b 100%);
                        box-shadow: 0 0 60px rgba(245,48,3,0.4), 0 0 120px rgba(245,48,3,0.15);">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>

            <h2 class="text-3xl font-bold mb-3 tracking-tight"
                style="color: var(--ui-content);">
                {{ config('app.name', 'Laravel') }}
            </h2>

            <p class="text-sm leading-relaxed max-w-xs"
               style="color: var(--ui-content-muted);">
                {!! __('public.platform_desc') !!}
            </p>

            {{-- Separador --}}
            <div class="flex items-center gap-3 mt-8">
                <div class="w-8 h-px" style="background: var(--auth-divider);"></div>
                <div class="w-1.5 h-1.5 rounded-full" style="background: rgba(245,48,3,0.7);"></div>
                <div class="w-8 h-px" style="background: var(--auth-divider);"></div>
            </div>

        </div>
    </div>

</div>

@livewireScripts

<script>
// Wave canvas — pantalla completa
(function () {
    const canvas = document.getElementById('auth-wave-canvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    let W, H, t = 0;

    function resize() {
        W = canvas.width  = canvas.offsetWidth;
        H = canvas.height = canvas.offsetHeight;
    }

    function wave(offsetY, color, alpha, speed, amp, freq, lw) {
        ctx.beginPath();
        ctx.strokeStyle = color;
        ctx.globalAlpha = alpha;
        ctx.lineWidth   = lw;
        for (let i = 0; i <= 300; i++) {
            const pr = i / 300;
            const py = offsetY
                + Math.sin(pr * freq       + t * speed)            * amp
                + Math.sin(pr * freq * 1.7 + t * speed * 0.8 + 1)  * amp * 0.45
                + Math.sin(pr * freq * 0.5 + t * speed * 1.2 + 2)  * amp * 0.28;
            i === 0 ? ctx.moveTo(pr * W, py) : ctx.lineTo(pr * W, py);
        }
        ctx.stroke();
        ctx.globalAlpha = 1;
    }

    function animate() {
        ctx.clearRect(0, 0, W, H);
        const dark = document.documentElement.classList.contains('dark');

        for (let i = 0; i < 25; i++) {
            let color, alpha;
            if (dark) {
                const r = Math.round(40 + i * 5), g = Math.round(175 - i * 3);
                color = `rgb(${r},${g},255)`;
                alpha = 0.04 + (i % 5) * 0.01;
            } else {
                color = `rgb(${80 + i * 3},${100 + i * 2},${200 - i})`;
                alpha = (0.04 + (i % 5) * 0.01) * 0.6;
            }
            wave(H * 0.3 + (i - 12) * 20, color, alpha,
                0.35 + i * 0.018, 60 + i * 5, 2.3 + i * 0.16, 0.35 + (i % 4) * 0.18);
        }

        for (let i = 0; i < 50; i++) {
            let color, alpha;
            if (dark) {
                const fade = Math.max(0, 255 - i * 3);
                color = `rgb(255,${fade},${Math.round(fade * 0.8)})`;
                alpha = 0.025 + (i % 7) * 0.01;
            } else {
                color = `rgb(${200 + i},${80 + i * 2},${90 + i})`;
                alpha = (0.025 + (i % 7) * 0.01) * 0.4;
            }
            wave(H * 0.55 + (i - 25) * 13, color, alpha,
                0.22 + i * 0.012, 75 + i * 3, 1.7 + i * 0.1, 0.28 + (i % 4) * 0.14);
        }

        for (let i = 0; i < 10; i++) {
            const a = 0.14 + Math.sin(t * 0.45 + i) * 0.07;
            const color = dark
                ? (i % 2 === 0 ? `rgba(100,220,255,${a})` : `rgba(255,255,255,${a * 0.65})`)
                : `rgba(100,120,230,${a * 0.5})`;
            wave(H * 0.42 + (i - 5) * 32, color, 1,
                0.48 + i * 0.045, 95 + i * 11, 2.0 + i * 0.28, 1.1 + (i % 3) * 0.38);
        }

        t += 0.008;
        requestAnimationFrame(animate);
    }

    window.addEventListener('resize', resize);
    resize();
    animate();
})();
</script>
</body>
</html>
