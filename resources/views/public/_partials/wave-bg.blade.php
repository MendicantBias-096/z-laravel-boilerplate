{{-- Fondo: gradiente + patrón de puntos --}}
<div class="absolute inset-0 pointer-events-none">
    <div class="absolute inset-0"
         style="background: radial-gradient(ellipse 80% 60% at 80% 40%, #4a0a0a 0%, transparent 60%),
                           radial-gradient(ellipse 60% 50% at 10% 60%, #0a0a3a 0%, transparent 55%),
                           linear-gradient(135deg, #080c18 0%, #0f1629 40%, #1a0a1a 70%, #2a0808 100%);"></div>
    <div class="absolute inset-0 opacity-20"
         style="background-image: radial-gradient(circle, rgba(255,255,255,0.15) 1px, transparent 1px);
                background-size: 28px 28px;"></div>
    <div class="absolute top-0 left-0 right-0 h-32"
         style="background: linear-gradient(to bottom, rgba(8,12,24,0.85) 0%, transparent 100%);"></div>
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
        for (let i = 0; i < 30; i++) {
            const r = Math.round(40 + i * 4), g = Math.round(180 - i * 2);
            drawWave(H * 0.3 + (i - 15) * 18, `rgb(${r},${g},255)`,
                0.04 + (i % 6) * 0.01, 0.4 + i * 0.02, 60 + i * 4, 2.5 + i * 0.15, 0.4 + (i % 5) * 0.2);
        }
        for (let i = 0; i < 60; i++) {
            const fade = Math.max(0, 255 - i * 2);
            drawWave(H * 0.5 + (i - 30) * 12, `rgb(255,${fade},${Math.round(fade * 0.8)})`,
                0.03 + (i % 8) * 0.01, 0.25 + i * 0.015, 80 + i * 3, 1.8 + i * 0.1, 0.3 + (i % 4) * 0.15);
        }
        for (let i = 0; i < 12; i++) {
            const a = 0.15 + Math.sin(t * 0.5 + i) * 0.08;
            drawWave(H * 0.4 + (i - 6) * 30,
                i % 2 === 0 ? `rgba(100,220,255,${a})` : `rgba(255,255,255,${a * 0.7})`,
                1, 0.5 + i * 0.05, 100 + i * 10, 2 + i * 0.3, 1.2 + (i % 3) * 0.4);
        }
        t += 0.008;
        requestAnimationFrame(animate);
    }

    window.addEventListener('resize', resize);
    resize();
    animate();
})();
</script>
