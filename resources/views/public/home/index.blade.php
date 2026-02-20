<div class="relative min-h-screen bg-[#080c18] overflow-hidden font-['Inter',sans-serif]">

    @include('public._partials.wave-bg')
    @include('public._partials.navbar')

    {{-- HERO --}}
    <section class="relative z-10 flex flex-col items-center justify-center text-center
                    min-h-[calc(100vh-76px)] px-6 pb-16">

        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full mb-8 text-xs font-medium text-white/60"
             style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); backdrop-filter: blur(8px);">
            <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
            Plataforma en producción
        </div>

        <h1 class="text-5xl lg:text-7xl font-bold text-white leading-[1.08] tracking-tight mb-5 max-w-4xl">
            Landing Page
            <span class="block mt-2"
                  style="background: linear-gradient(90deg, #ff6b47 0%, #f53003 50%, #c0392b 100%);
                         -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                Abstract Background
            </span>
        </h1>

        <p class="text-base lg:text-lg text-white/45 leading-relaxed mb-10 max-w-lg">
            It is a long established fact that a reader will be distracted by the readable content
            of a page when looking at its layout. The point of using Lorem Ipsum is that it has
            a more-or-less normal distribution of letters, as opposed to using 'Content here'.
        </p>

        <div class="flex items-center justify-center gap-3 flex-wrap">
            <a href="{{ route('register') }}"
               class="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl text-sm font-semibold text-white"
               style="background: linear-gradient(135deg, #f53003 0%, #c0392b 100%);
                      box-shadow: 0 4px 24px rgba(245,48,3,0.35); transition: all 0.25s ease;"
               onmouseover="this.style.boxShadow='0 8px 32px rgba(245,48,3,0.55)';this.style.transform='translateY(-2px)';"
               onmouseout="this.style.boxShadow='0 4px 24px rgba(245,48,3,0.35)';this.style.transform='translateY(0)';">
                Comenzar ahora
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
            <a href="{{ route('public.about') }}"
               class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl text-sm font-medium text-white/70 hover:text-white"
               style="border: 1px solid rgba(255,255,255,0.12); backdrop-filter: blur(8px);
                      transition: all 0.25s ease; background: rgba(255,255,255,0.04);"
               onmouseover="this.style.background='rgba(255,255,255,0.08)';this.style.borderColor='rgba(255,255,255,0.22)';"
               onmouseout="this.style.background='rgba(255,255,255,0.04)';this.style.borderColor='rgba(255,255,255,0.12)';">
                Saber más
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </a>
        </div>

        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-1.5 opacity-30">
            <span class="text-white text-xs tracking-widest uppercase">scroll</span>
            <div class="w-px h-8 bg-gradient-to-b from-white/60 to-transparent"></div>
        </div>

    </section>

    {{-- FEATURES --}}
    <section id="features" class="relative z-10 py-28 px-8 lg:px-16"
             style="background: linear-gradient(to bottom, transparent 0%, rgba(8,12,24,0.97) 25%, #080c18 100%);">
        <div class="max-w-5xl mx-auto">

            <div class="text-center mb-14">
                <p class="text-xs font-semibold tracking-[0.2em] uppercase mb-3" style="color:#f53003;">Plataforma</p>
                <h2 class="text-3xl lg:text-4xl font-bold text-white mb-4">Funcionalidades</h2>
                <p class="text-white/45 max-w-md mx-auto text-sm leading-relaxed">
                    Todo lo que necesitas en una sola plataforma, construida para escalar con tu negocio.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                @foreach([
                    ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'title' => 'Seguro por defecto', 'desc' => 'Autenticación robusta con Fortify, roles y permisos granulares integrados desde el primer día.'],
                    ['icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'title' => 'Ultra rápido', 'desc' => 'Construido sobre Laravel 12, Livewire 3 y Vite para una experiencia de usuario verdaderamente fluida.'],
                    ['icon' => 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z', 'title' => 'Modular', 'desc' => 'Arquitectura limpia con módulos públicos, privados y de administración completamente separados.'],
                ] as $f)
                <div class="p-6 rounded-2xl"
                     style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);
                            backdrop-filter:blur(8px);transition:all 0.3s ease;"
                     onmouseover="this.style.background='rgba(245,48,3,0.05)';this.style.borderColor='rgba(245,48,3,0.18)';this.style.transform='translateY(-4px)';"
                     onmouseout="this.style.background='rgba(255,255,255,0.03)';this.style.borderColor='rgba(255,255,255,0.07)';this.style.transform='translateY(0)';">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-5"
                         style="background:rgba(245,48,3,0.12);border:1px solid rgba(245,48,3,0.25);">
                        <svg class="w-5 h-5" style="color:#f97055;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $f['icon'] }}"/>
                        </svg>
                    </div>
                    <h3 class="text-white font-semibold mb-2">{{ $f['title'] }}</h3>
                    <p class="text-white/40 text-sm leading-relaxed">{{ $f['desc'] }}</p>
                </div>
                @endforeach
            </div>

        </div>
    </section>

    {{-- Footer --}}
    <footer class="relative z-10 py-8 px-8 lg:px-16 bg-[#080c18]"
            style="border-top:1px solid rgba(255,255,255,0.06);">
        <div class="max-w-5xl mx-auto flex items-center justify-between text-xs text-white/25">
            <span>&copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.</span>
            <div class="flex items-center gap-5">
                <a href="#" class="hover:text-white/55 transition-colors">Privacidad</a>
                <a href="#" class="hover:text-white/55 transition-colors">Términos</a>
            </div>
        </div>
    </footer>

</div>
