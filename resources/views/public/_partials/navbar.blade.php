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
        <span class="font-semibold text-base tracking-tight" style="color: var(--ui-content);">
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
                      {{ $active ? '' : 'dark:hover:bg-white/5 hover:bg-black/5' }}"
               style="{{ $active
                   ? 'color:var(--ui-content);background:var(--auth-link-bg-hover);'
                   : 'color:var(--ui-content-muted);' }}"
               @if(!$active)
               onmouseover="this.style.color='var(--ui-content)';"
               onmouseout="this.style.color='var(--ui-content-muted)';"
               @endif>
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

        {{-- Toggle dark / light --}}
        <button type="button" onclick="toggleTheme()"
                class="inline-flex items-center justify-center w-9 h-9 rounded-lg cursor-pointer transition-all duration-200"
                style="color: var(--ui-content-muted); border: 1px solid var(--auth-link-border);"
                onmouseover="this.style.background='var(--auth-link-bg)';this.style.color='var(--ui-content)';"
                onmouseout="this.style.background='transparent';this.style.color='var(--ui-content-muted)';"
                title="Cambiar tema">
            <svg class="theme-icon-sun w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/>
            </svg>
            <svg class="theme-icon-moon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z"/>
            </svg>
        </button>
        @auth
            <a href="{{ url('/dashboard') }}" wire:navigate
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium
                      transition-colors duration-200"
               style="color: var(--ui-content-muted);"
               onmouseover="this.style.color='var(--ui-content)';"
               onmouseout="this.style.color='var(--ui-content-muted)';">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>
        @else
            @if (Route::has('login'))
                <a href="{{ route('login') }}"
                   class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium
                          transition-all duration-200"
                   style="color: var(--ui-content-muted); border: 1px solid var(--auth-link-border);"
                   onmouseover="this.style.background='var(--auth-link-bg-hover)';this.style.borderColor='var(--auth-link-border-hover)';this.style.color='var(--ui-content)';"
                   onmouseout="this.style.background='transparent';this.style.borderColor='var(--auth-link-border)';this.style.color='var(--ui-content-muted)';">
                    Iniciar sesión
                </a>
            @endif
            @if (Route::has('register'))
                <a href="{{ route('register') }}"
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
