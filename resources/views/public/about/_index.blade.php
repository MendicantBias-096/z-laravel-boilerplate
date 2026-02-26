<div class="relative min-h-screen overflow-hidden font-['Inter',sans-serif]"
     style="background: var(--pub-base-bg);">

    @include('public._partials.wave-bg')
    @include('public._partials.navbar')

    {{-- HERO --}}
    <section class="relative z-10 flex flex-col items-center justify-center text-center
                    min-h-[55vh] px-6 pt-4 pb-20">

        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full mb-7 text-xs font-medium"
             style="background: var(--auth-link-bg); border: 1px solid var(--auth-link-border);
                    backdrop-filter: blur(8px); color: var(--ui-content-muted);">
            <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
            Conócenos
        </div>

        <h1 class="text-5xl lg:text-6xl font-bold leading-[1.08] tracking-tight mb-5 max-w-3xl"
            style="color: var(--ui-content);">
            Sobre
            <span style="background:linear-gradient(90deg,#ff6b47 0%,#f53003 50%,#c0392b 100%);
                         -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">
                Nosotros
            </span>
        </h1>

        <p class="text-base leading-relaxed max-w-md" style="color: var(--ui-content-muted);">
            Somos un equipo apasionado por construir herramientas que simplifican el desarrollo moderno,
            combinando rendimiento, seguridad y una experiencia de desarrollador excepcional.
        </p>

    </section>

    {{-- MISIÓN / VISIÓN --}}
    <section class="relative z-10 py-20 px-8 lg:px-16"
             style="background: linear-gradient(to bottom, transparent 0%, var(--pub-base-bg) 20%, var(--pub-base-bg) 100%);">
        <div class="max-w-5xl mx-auto">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-16">

                {{-- Misión --}}
                <div class="p-8 rounded-2xl"
                     style="background:rgba(245,48,3,0.05);border:1px solid rgba(245,48,3,0.15);backdrop-filter:blur(8px);">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-5"
                         style="background:rgba(245,48,3,0.15);border:1px solid rgba(245,48,3,0.3);">
                        <svg class="w-5 h-5" style="color:#f97055;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-xl mb-3" style="color: var(--ui-content);">Nuestra Misión</h3>
                    <p class="text-sm leading-relaxed" style="color: var(--ui-content-muted);">
                        Proporcionar una base sólida y moderna para el desarrollo de aplicaciones Laravel,
                        reduciendo el tiempo de configuración inicial y permitiendo a los equipos enfocarse
                        en lo que realmente importa: crear valor para sus usuarios.
                    </p>
                </div>

                {{-- Visión --}}
                <div class="p-8 rounded-2xl"
                     style="background: var(--pub-card-bg); border: 1px solid var(--pub-card-border); backdrop-filter:blur(8px);">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-5"
                         style="background:rgba(100,220,255,0.08);border:1px solid rgba(100,220,255,0.2);">
                        <svg class="w-5 h-5" style="color:#64dcff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-xl mb-3" style="color: var(--ui-content);">Nuestra Visión</h3>
                    <p class="text-sm leading-relaxed" style="color: var(--ui-content-muted);">
                        Ser el boilerplate de referencia para equipos que desarrollan aplicaciones Laravel
                        empresariales, ofreciendo las mejores prácticas, patrones modernos y una arquitectura
                        modular que escala con el crecimiento de cada proyecto.
                    </p>
                </div>

            </div>

            {{-- Valores --}}
            <div class="text-center mb-12">
                <p class="text-xs font-semibold tracking-[0.2em] uppercase mb-3" style="color:#f53003;">Principios</p>
                <h2 class="text-3xl font-bold" style="color: var(--ui-content);">Nuestros valores</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach([
                    ['icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',    'label' => 'Calidad',      'color' => '#f97055', 'bg' => 'rgba(245,48,3,0.1)',     'bd' => 'rgba(245,48,3,0.2)'],
                    ['icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'label' => 'Seguridad', 'color' => '#64dcff', 'bg' => 'rgba(100,220,255,0.08)', 'bd' => 'rgba(100,220,255,0.18)'],
                    ['icon' => 'M13 10V3L4 14h7v7l9-11h-7z',                         'label' => 'Rendimiento', 'color' => '#a78bfa', 'bg' => 'rgba(167,139,250,0.08)', 'bd' => 'rgba(167,139,250,0.18)'],
                    ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'label' => 'Comunidad', 'color' => '#34d399', 'bg' => 'rgba(52,211,153,0.08)', 'bd' => 'rgba(52,211,153,0.18)'],
                ] as $v)
                <div class="p-6 rounded-2xl text-center"
                     style="background: var(--pub-card-bg); border: 1px solid var(--pub-card-border);
                            transition:all 0.3s ease;"
                     onmouseover="this.style.background='{{ $v['bg'] }}';this.style.borderColor='{{ $v['bd'] }}';this.style.transform='translateY(-4px)';"
                     onmouseout="this.style.background='var(--pub-card-bg)';this.style.borderColor='var(--pub-card-border)';this.style.transform='translateY(0)';">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-4"
                         style="background:{{ $v['bg'] }};border:1px solid {{ $v['bd'] }};">
                        <svg class="w-6 h-6" style="color:{{ $v['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $v['icon'] }}"/>
                        </svg>
                    </div>
                    <h4 class="font-semibold text-sm" style="color: var(--ui-content);">{{ $v['label'] }}</h4>
                </div>
                @endforeach
            </div>

        </div>
    </section>

    {{-- CTA final --}}
    <section class="relative z-10 py-20 px-8 text-center"
             style="background: var(--pub-base-bg);">
        <div class="max-w-xl mx-auto">
            <h2 class="text-2xl lg:text-3xl font-bold mb-4" style="color: var(--ui-content);">¿Listo para empezar?</h2>
            <p class="text-sm leading-relaxed mb-8" style="color: var(--ui-content-muted);">
                Únete a la plataforma y descubre por qué equipos de todo el mundo confían en nosotros.
            </p>
            <div class="flex items-center justify-center gap-3">
                <a href="{{ route('register') }}"
                   class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl text-sm font-semibold text-white"
                   style="background:linear-gradient(135deg,#f53003 0%,#c0392b 100%);
                          box-shadow:0 4px 24px rgba(245,48,3,0.35);transition:all 0.25s ease;"
                   onmouseover="this.style.boxShadow='0 8px 32px rgba(245,48,3,0.55)';this.style.transform='translateY(-2px)';"
                   onmouseout="this.style.boxShadow='0 4px 24px rgba(245,48,3,0.35)';this.style.transform='translateY(0)';">
                    Crear cuenta gratis
                </a>
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl text-sm font-medium
                          transition-all duration-200"
                   style="border: 1px solid var(--auth-link-border); background: var(--auth-link-bg);
                          color: var(--ui-content-muted);"
                   onmouseover="this.style.background='var(--auth-link-bg-hover)';this.style.borderColor='var(--auth-link-border-hover)';this.style.color='var(--ui-content)';"
                   onmouseout="this.style.background='var(--auth-link-bg)';this.style.borderColor='var(--auth-link-border)';this.style.color='var(--ui-content-muted)';">
                    Iniciar sesión
                </a>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="relative z-10 py-8 px-8 lg:px-16"
            style="background: var(--pub-base-bg); border-top: 1px solid var(--auth-divider);">
        <div class="max-w-5xl mx-auto flex items-center justify-between text-xs"
             style="color: var(--ui-content-subtle);">
            <span>&copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.</span>
            <div class="flex items-center gap-5">
                <a href="#" class="transition-colors"
                   onmouseover="this.style.color='var(--ui-content-muted)';"
                   onmouseout="this.style.color='var(--ui-content-subtle)';">Privacidad</a>
                <a href="#" class="transition-colors"
                   onmouseover="this.style.color='var(--ui-content-muted)';"
                   onmouseout="this.style.color='var(--ui-content-subtle)';">Términos</a>
            </div>
        </div>
    </footer>

</div>
