<div class="relative min-h-screen overflow-hidden font-['Inter',sans-serif]"
     style="background: var(--pub-base-bg);">

    @include('platform::public._partials.wave-bg')
    @include('platform::public._partials.navbar')

    {{-- HERO --}}
    <section class="relative z-10 flex flex-col items-center justify-center text-center
                    min-h-[calc(100vh-76px)] px-6 pb-16">

        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full mb-8 text-xs font-medium"
             style="background: var(--auth-link-bg); border: 1px solid var(--auth-link-border);
                    backdrop-filter: blur(8px); color: var(--ui-content-muted);">
            <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
            {{ __('platform::public.hero_badge') }}
        </div>

        <h1 class="text-5xl lg:text-7xl font-bold leading-tight tracking-tight mb-6 max-w-4xl"
            style="color: var(--ui-content);">
            {{ __('platform::public.hero_title_1') }}
            <span class="block mt-4"
                  style="background: linear-gradient(90deg, #ff6b47 0%, #f53003 50%, #c0392b 100%);
                         -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                {{ __('platform::public.hero_title_2') }}
            </span>
        </h1>

        <p class="text-base lg:text-lg leading-relaxed mb-10 max-w-lg"
           style="color: var(--ui-content-muted);">
            {{ __('platform::public.hero_description') }}
        </p>

        <div class="flex items-center justify-center gap-3 flex-wrap">
            <a href="{{ route('register') }}"
               class="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl text-sm font-semibold text-white"
               style="background: linear-gradient(135deg, #f53003 0%, #c0392b 100%);
                      box-shadow: 0 4px 24px rgba(245,48,3,0.35); transition: all 0.25s ease;"
               onmouseover="this.style.boxShadow='0 8px 32px rgba(245,48,3,0.55)';this.style.transform='translateY(-2px)';"
               onmouseout="this.style.boxShadow='0 4px 24px rgba(245,48,3,0.35)';this.style.transform='translateY(0)';">
                {{ __('platform::public.hero_cta') }}
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
            <a href="{{ route('platform::public.about') }}"
               class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl text-sm font-medium
                      transition-all duration-200"
               style="border: 1px solid var(--auth-link-border); backdrop-filter: blur(8px);
                      background: var(--auth-link-bg); color: var(--ui-content-muted);"
               onmouseover="this.style.background='var(--auth-link-bg-hover)';this.style.borderColor='var(--auth-link-border-hover)';this.style.color='var(--ui-content)';"
               onmouseout="this.style.background='var(--auth-link-bg)';this.style.borderColor='var(--auth-link-border)';this.style.color='var(--ui-content-muted)';">
                {{ __('platform::public.hero_secondary') }}
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </a>
        </div>

        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-1.5 opacity-30">
            <span class="text-xs tracking-widest uppercase" style="color: var(--ui-content);">scroll</span>
            <div class="w-px h-8 bg-gradient-to-b from-current to-transparent" style="color: var(--ui-content-muted);"></div>
        </div>

    </section>

    {{-- FEATURES --}}
    <section id="features" class="relative z-10 py-28 px-8 lg:px-16"
             style="background: linear-gradient(to bottom, transparent 0%, var(--pub-base-bg) 25%, var(--pub-base-bg) 100%);">
        <div class="max-w-5xl mx-auto">

            <div class="text-center mb-14">
                <p class="text-xs font-semibold tracking-[0.2em] uppercase mb-3" style="color:#f53003;">{{ __('platform::public.features_label') }}</p>
                <h2 class="text-3xl lg:text-4xl font-bold mb-4" style="color: var(--ui-content);">{{ __('platform::public.features_title') }}</h2>
                <p class="max-w-md mx-auto text-sm leading-relaxed" style="color: var(--ui-content-muted);">
                    {{ __('platform::public.features_subtitle') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                @foreach([
                    ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'title' => __('platform::public.feat_secure'), 'desc' => __('platform::public.feat_secure_desc')],
                    ['icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'title' => __('platform::public.feat_fast'), 'desc' => __('platform::public.feat_fast_desc')],
                    ['icon' => 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z', 'title' => __('platform::public.feat_modular'), 'desc' => __('platform::public.feat_modular_desc')],
                ] as $f)
                <div class="p-6 rounded-2xl"
                     style="background: var(--pub-card-bg); border: 1px solid var(--pub-card-border);
                            backdrop-filter:blur(8px); transition:all 0.3s ease;"
                     onmouseover="this.style.background='rgba(245,48,3,0.05)';this.style.borderColor='rgba(245,48,3,0.18)';this.style.transform='translateY(-4px)';"
                     onmouseout="this.style.background='var(--pub-card-bg)';this.style.borderColor='var(--pub-card-border)';this.style.transform='translateY(0)';">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-5"
                         style="background:rgba(245,48,3,0.12);border:1px solid rgba(245,48,3,0.25);">
                        <svg class="w-5 h-5" style="color:#f97055;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $f['icon'] }}"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold mb-2" style="color: var(--ui-content);">{{ $f['title'] }}</h3>
                    <p class="text-sm leading-relaxed" style="color: var(--ui-content-muted);">{{ $f['desc'] }}</p>
                </div>
                @endforeach
            </div>

        </div>
    </section>

    {{-- STACK --}}
    <section class="relative z-10 py-28 px-8 lg:px-16" style="background: var(--pub-base-bg);">
        <div class="max-w-5xl mx-auto">

            <div class="text-center mb-14">
                <p class="text-xs font-semibold tracking-[0.2em] uppercase mb-3" style="color:#f53003;">{{ __('platform::public.stack_label') }}</p>
                <h2 class="text-3xl lg:text-4xl font-bold mb-4" style="color: var(--ui-content);">{{ __('platform::public.stack_title') }}</h2>
                <p class="max-w-md mx-auto text-sm leading-relaxed" style="color: var(--ui-content-muted);">
                    {{ __('platform::public.stack_subtitle') }}
                </p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                @foreach ($stack as $tech)
                <div class="p-5 rounded-2xl text-center"
                     style="background: var(--pub-card-bg); border: 1px solid var(--pub-card-border);
                            backdrop-filter:blur(8px); transition:all 0.3s ease;"
                     onmouseover="this.style.background='rgba(245,48,3,0.05)';this.style.borderColor='rgba(245,48,3,0.18)';this.style.transform='translateY(-4px)';"
                     onmouseout="this.style.background='var(--pub-card-bg)';this.style.borderColor='var(--pub-card-border)';this.style.transform='translateY(0)';">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center mx-auto mb-3"
                         style="background:rgba(245,48,3,0.12);border:1px solid rgba(245,48,3,0.25);">
                        <svg class="w-5 h-5" style="color:#f97055;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $tech['icon'] }}"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold" style="color: var(--ui-content);">{{ $tech['name'] }}</p>
                    <p class="text-xs mt-1" style="color: var(--ui-content-muted);">{{ $tech['version'] }}</p>
                </div>
                @endforeach
            </div>

        </div>
    </section>

    {{-- Footer --}}
    <footer class="relative z-10 py-8 px-8 lg:px-16"
            style="background: var(--pub-base-bg); border-top: 1px solid var(--auth-divider);">
        <div class="max-w-5xl mx-auto flex items-center justify-between text-xs"
             style="color: var(--ui-content-subtle);">
            <span>&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('platform::public.footer_rights') }}</span>
            <div class="flex items-center gap-5">
                <a href="#" class="transition-colors"
                   onmouseover="this.style.color='var(--ui-content-muted)';"
                   onmouseout="this.style.color='var(--ui-content-subtle)';">{{ __('platform::public.footer_privacy') }}</a>
                <a href="#" class="transition-colors"
                   onmouseover="this.style.color='var(--ui-content-muted)';"
                   onmouseout="this.style.color='var(--ui-content-subtle)';">{{ __('platform::public.footer_terms') }}</a>
            </div>
        </div>
    </footer>

</div>
