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
</head>
<body class="antialiased bg-[#070a14] font-['Inter',sans-serif]">

<div class="min-h-screen flex">

    {{-- ═══════════════════════════════════════
         PANEL IZQUIERDO — Formulario
    ════════════════════════════════════════ --}}
    <div class="flex-1 flex flex-col items-center justify-center px-8 lg:px-16 py-12"
         style="background: #070a14;">

        {{-- Form slot --}}
        <div class="w-full max-w-sm">
            {{ $slot }}
        </div>

        {{-- Volver — mismo ancho que el form --}}
        <div class="w-full max-w-sm mt-10">
            <a href="{{ route('home') }}" wire:navigate
               class="inline-flex items-center gap-1.5 text-xs text-white/25
                      hover:text-white/50 transition-colors duration-200">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al inicio
            </a>
        </div>

    </div>

    {{-- ═══════════════════════════════════════
         PANEL DERECHO — Branding + ondas
    ════════════════════════════════════════ --}}
    <div class="hidden lg:flex flex-1 relative overflow-hidden items-center justify-center"
         style="background: linear-gradient(135deg, #080c18 0%, #0f1629 40%, #1a0a1a 70%, #2a0808 100%);">

        {{-- Gradientes de color --}}
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute inset-0"
                 style="background: radial-gradient(ellipse 90% 60% at 75% 50%, #4a0a0a 0%, transparent 60%),
                                   radial-gradient(ellipse 70% 60% at 20% 50%, #08083a 0%, transparent 55%);"></div>
            <div class="absolute inset-0 opacity-[0.12]"
                 style="background-image: radial-gradient(circle, rgba(255,255,255,0.2) 1px, transparent 1px);
                        background-size: 28px 28px;"></div>
        </div>

        {{-- Canvas: ondas animadas confinadas al panel --}}
        <canvas id="auth-wave-canvas"
                class="absolute inset-0 w-full h-full pointer-events-none"
                style="opacity: 0.9;"></canvas>

        {{-- Branding centrado --}}
        <div class="relative z-10 flex flex-col items-center text-center px-12">

            {{-- Logo grande --}}
            <div class="w-20 h-20 rounded-3xl flex items-center justify-center mb-7"
                 style="background: linear-gradient(135deg, #f53003 0%, #c0392b 100%);
                        box-shadow: 0 0 60px rgba(245,48,3,0.4), 0 0 120px rgba(245,48,3,0.15);">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>

            <h2 class="text-3xl font-bold text-white mb-3 tracking-tight">
                {{ config('app.name', 'Laravel') }}
            </h2>

            <p class="text-white/40 text-sm leading-relaxed max-w-xs">
                Plataforma de gestión moderna.<br>
                Segura, rápida y lista para escalar.
            </p>

            {{-- Separador --}}
            <div class="flex items-center gap-3 mt-8">
                <div class="w-8 h-px" style="background: rgba(255,255,255,0.15);"></div>
                <div class="w-1.5 h-1.5 rounded-full" style="background: rgba(245,48,3,0.7);"></div>
                <div class="w-8 h-px" style="background: rgba(255,255,255,0.15);"></div>
            </div>

        </div>

    </div>

</div>

@livewireScripts

<script>
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

        /* Grupo 1: azul-cian */
        for (let i = 0; i < 25; i++) {
            const r = Math.round(40 + i * 5), g = Math.round(175 - i * 3);
            wave(H * 0.3 + (i - 12) * 20, `rgb(${r},${g},255)`,
                0.04 + (i % 5) * 0.01, 0.35 + i * 0.018,
                60 + i * 5, 2.3 + i * 0.16, 0.35 + (i % 4) * 0.18);
        }

        /* Grupo 2: blanco-rosado */
        for (let i = 0; i < 50; i++) {
            const fade = Math.max(0, 255 - i * 3);
            wave(H * 0.55 + (i - 25) * 13, `rgb(255,${fade},${Math.round(fade * 0.8)})`,
                0.025 + (i % 7) * 0.01, 0.22 + i * 0.012,
                75 + i * 3, 1.7 + i * 0.1, 0.28 + (i % 4) * 0.14);
        }

        /* Grupo 3: destellos */
        for (let i = 0; i < 10; i++) {
            const a = 0.14 + Math.sin(t * 0.45 + i) * 0.07;
            wave(H * 0.42 + (i - 5) * 32,
                i % 2 === 0 ? `rgba(100,220,255,${a})` : `rgba(255,255,255,${a * 0.65})`,
                1, 0.48 + i * 0.045,
                95 + i * 11, 2.0 + i * 0.28, 1.1 + (i % 3) * 0.38);
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
