@php
    $links = [
        ['label' => 'Inicio',   'route' => 'home'],
        ['label' => 'Nosotros', 'route' => 'public.about'],
    ];
@endphp

<nav class="relative z-20 flex items-center justify-between px-8 lg:px-16 py-5">

    {{-- Logo --}}
    <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2.5 shrink-0">
        <div class="w-8 h-8 rounded-lg flex items-center justify-center"
             style="background: linear-gradient(135deg, #f53003 0%, #c0392b 100%);
                    box-shadow: 0 0 16px rgba(245,48,3,0.5);">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                      d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
        </div>
        <span class="text-white font-semibold text-base tracking-tight">
            {{ config('app.name', 'Laravel') }}
        </span>
    </a>

    {{-- Links centrados --}}
    <ul class="hidden md:flex items-center gap-1 absolute left-1/2 -translate-x-1/2">
        @foreach($links as $link)
        <li>
            @php $active = request()->routeIs($link['route']); @endphp
            <a href="{{ route($link['route']) }}" wire:navigate
               class="px-4 py-2 rounded-lg text-sm block transition-all duration-200
                      {{ $active
                           ? 'text-white bg-white/8'
                           : 'text-white/55 hover:text-white hover:bg-white/5' }}"
               @if($active) style="background:rgba(255,255,255,0.08);" @endif>
                {{ $link['label'] }}
                @if($active)
                <span class="inline-block w-1 h-1 rounded-full bg-red-500 ml-1 align-middle"></span>
                @endif
            </a>
        </li>
        @endforeach
    </ul>

    {{-- Acciones --}}
    <div class="flex items-center gap-2 shrink-0">
        @auth
            <a href="{{ url('/dashboard') }}" wire:navigate
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium
                      text-white/80 hover:text-white transition-colors duration-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>
        @else
            @if (Route::has('login'))
                <a href="{{ route('login') }}" wire:navigate
                   class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium
                          text-white/70 hover:text-white transition-all duration-200"
                   style="border: 1px solid rgba(255,255,255,0.1);"
                   onmouseover="this.style.background='rgba(255,255,255,0.07)';this.style.borderColor='rgba(255,255,255,0.2)';"
                   onmouseout="this.style.background='transparent';this.style.borderColor='rgba(255,255,255,0.1)';">
                    Iniciar sesión
                </a>
            @endif
            @if (Route::has('register'))
                <a href="{{ route('register') }}" wire:navigate
                   class="inline-flex items-center gap-1.5 px-5 py-2 rounded-lg text-sm font-semibold
                          text-white transition-all duration-200"
                   style="background: linear-gradient(135deg, #f53003 0%, #c0392b 100%);
                          transition: all 0.25s ease;"
                   onmouseover="this.style.boxShadow='0 0 22px rgba(245,48,3,0.55)';this.style.transform='translateY(-1px)';"
                   onmouseout="this.style.boxShadow='none';this.style.transform='translateY(0)';">
                    Registrarse
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                              d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            @endif
        @endauth
    </div>

</nav>
