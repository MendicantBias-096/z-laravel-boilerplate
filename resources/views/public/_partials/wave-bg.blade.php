{{-- Fondo: gradiente + patrón de puntos --}}
<div class="absolute inset-0 pointer-events-none">
    <div class="absolute inset-0"
         style="background: radial-gradient(ellipse 80% 60% at 80% 40%, var(--pub-grad-a) 0%, transparent 60%),
                           radial-gradient(ellipse 60% 50% at 10% 60%, var(--pub-grad-b) 0%, transparent 55%),
                           var(--pub-base-bg);"></div>
    <div class="absolute inset-0"
         style="background-image: radial-gradient(circle, var(--pub-dots) 1px, transparent 1px);
                background-size: 28px 28px;"></div>
    <div class="absolute top-0 left-0 right-0 h-32"
         style="background: linear-gradient(to bottom, var(--pub-base-bg) 0%, transparent 100%); opacity: 0.7;"></div>
</div>

{{-- Canvas: ondas animadas --}}
<canvas class="wave-canvas absolute inset-0 w-full h-full pointer-events-none" style="opacity:0.85;"></canvas>

<script>
(function () {
    // Espera al canvas más cercano dentro del mismo ancestro
    const canvas = document.currentScript.previousElementSibling;
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    let W, H, t = 0;

    function resize() {
        W = canvas.width  = canvas.offsetWidth;
        H = canvas.height = canvas.offsetHeight;
    }

    function drawWave(offsetY, color, alpha, speed, amplitude, frequency, lineWidth) {
        ctx.beginPath();
        ctx.strokeStyle = color;
        ctx.globalAlpha = alpha;
        ctx.lineWidth   = lineWidth;
        for (let i = 0; i <= 300; i++) {
            const pr = i / 300;
            const py = offsetY
                + Math.sin(pr * frequency       + t * speed)            * amplitude
                + Math.sin(pr * frequency * 1.7 + t * speed * 0.8 + 1)  * amplitude * 0.5
                + Math.sin(pr * frequency * 0.5 + t * speed * 1.3 + 2)  * amplitude * 0.3;
            i === 0 ? ctx.moveTo(pr * W, py) : ctx.lineTo(pr * W, py);
        }
        ctx.stroke();
        ctx.globalAlpha = 1;
    }

    function animate() {
        ctx.clearRect(0, 0, W, H);
        const dark = document.documentElement.classList.contains('dark');

        /* Grupo 1: azul-cian (dark) / azul pálido (light) */
        for (let i = 0; i < 30; i++) {
            let color, alpha;
            if (dark) {
                const r = Math.round(40 + i * 4), g = Math.round(180 - i * 2);
                color = `rgb(${r},${g},255)`;
                alpha = 0.04 + (i % 6) * 0.01;
            } else {
                color = `rgb(${80 + i * 3},${100 + i * 2},${200 - i})`;
                alpha = (0.04 + (i % 6) * 0.01) * 0.6;
            }
            drawWave(H * 0.3 + (i - 15) * 18, color, alpha,
                0.4 + i * 0.02, 60 + i * 4, 2.5 + i * 0.15, 0.4 + (i % 5) * 0.2);
        }

        /* Grupo 2: blanco-rosado (dark) / rosa pálido (light) */
        for (let i = 0; i < 60; i++) {
            let color, alpha;
            if (dark) {
                const fade = Math.max(0, 255 - i * 2);
                color = `rgb(255,${fade},${Math.round(fade * 0.8)})`;
                alpha = 0.03 + (i % 8) * 0.01;
            } else {
                color = `rgb(${200 + i},${80 + i * 2},${90 + i})`;
                alpha = (0.03 + (i % 8) * 0.01) * 0.4;
            }
            drawWave(H * 0.5 + (i - 30) * 12, color, alpha,
                0.25 + i * 0.015, 80 + i * 3, 1.8 + i * 0.1, 0.3 + (i % 4) * 0.15);
        }

        /* Grupo 3: destellos */
        for (let i = 0; i < 12; i++) {
            const a = 0.15 + Math.sin(t * 0.5 + i) * 0.08;
            let color;
            if (dark) {
                color = i % 2 === 0 ? `rgba(100,220,255,${a})` : `rgba(255,255,255,${a * 0.7})`;
            } else {
                color = `rgba(100,120,230,${a * 0.5})`;
            }
            drawWave(H * 0.4 + (i - 6) * 30, color, 1,
                0.5 + i * 0.05, 100 + i * 10, 2 + i * 0.3, 1.2 + (i % 3) * 0.4);
        }

        t += 0.008;
        requestAnimationFrame(animate);
    }

    window.addEventListener('resize', resize);
    resize();
    animate();
})();
</script>
