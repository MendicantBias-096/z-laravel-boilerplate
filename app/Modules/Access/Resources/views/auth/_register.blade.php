<div>
    {{-- Header --}}
    <div class="mb-7 text-center">
        <h1 class="text-2xl font-bold mb-1.5" style="color: var(--ui-content);">{{ __('platform::public.register_title') }}</h1>
        <p class="text-sm" style="color: var(--ui-content-muted);">{{ __('platform::public.register_subtitle') }}</p>
    </div>

    <form wire:submit="register" class="flex flex-col gap-4" novalidate>

        {{-- Campo trampa: invisible para una persona, irresistible para un bot.
             Oculto con posicionamiento y no con `display:none` ni `hidden`,
             porque un bot que mire el estilo descarta lo segundo. `tabindex`
             y `aria-hidden` lo sacan del tabulador y del lector de pantalla,
             que es lo que lo hace invisible de verdad para quien navega sin
             ratón. --}}
        <div aria-hidden="true" style="position:absolute; left:-9999px; top:auto; width:1px; height:1px; overflow:hidden;">
            <label for="website">{{ __('platform::public.website') }}</label>
            <input type="text" id="website" name="website" wire:model="website" tabindex="-1" autocomplete="off">
        </div>

        {{-- Nombre y Apellido --}}
        <div class="grid grid-cols-2 gap-3">
            <x-ui.auth-input
                wire:model="first_name"
                label="{{ __('platform::public.first_name') }}"
                placeholder="{{ __('platform::public.first_name_hint') }}"
                autocomplete="given-name"
            />
            <x-ui.auth-input
                wire:model="last_name"
                label="{{ __('platform::public.last_name') }}"
                placeholder="{{ __('platform::public.last_name_hint') }}"
                autocomplete="family-name"
            />
        </div>

        {{-- Usuario y Email --}}
        <div class="grid grid-cols-2 gap-3">
            <x-ui.auth-input
                wire:model="username"
                label="{{ __('platform::public.username') }}"
                placeholder="{{ __('platform::public.username_hint') }}"
                autocomplete="username"
            />
            <x-ui.auth-input
                wire:model="email"
                type="email"
                label="{{ __('platform::public.email') }}"
                placeholder="tu@email.com"
                autocomplete="email"
            />
        </div>

        {{-- Password --}}
        <x-ui.auth-password
            wire:model="password"
            label="{{ __('platform::public.password') }}"
            placeholder="{{ __('platform::public.password_hint') }}"
            autocomplete="new-password"
        />

        {{-- Confirm Password --}}
        <x-ui.auth-password
            wire:model="password_confirmation"
            label="{{ __('platform::public.confirm_password') }}"
            placeholder="{{ __('platform::public.confirm_hint') }}"
            autocomplete="new-password"
        />

        {{-- Submit --}}
        <button type="submit"
                wire:loading.attr="disabled"
                class="w-full h-12 rounded-xl text-sm font-semibold text-white mt-1
                       relative overflow-hidden transition-all duration-200"
                style="background:linear-gradient(135deg,#f53003 0%,#c0392b 100%);
                       box-shadow:0 4px 20px rgba(245,48,3,0.35);"
                onmouseover="this.style.boxShadow='0 8px 28px rgba(245,48,3,0.52)';this.style.transform='translateY(-1px)';"
                onmouseout="this.style.boxShadow='0 4px 20px rgba(245,48,3,0.35)';this.style.transform='translateY(0)';">
            <span wire:loading.class="opacity-0 -translate-y-2"
                  class="absolute inset-0 flex items-center justify-center transition-all duration-200 ease-in-out">
                {{ __('platform::public.register_button') }}
            </span>
            <span wire:loading.class.remove="opacity-0 translate-y-2"
                  class="absolute inset-0 flex items-center justify-center gap-2 transition-all duration-200 ease-in-out opacity-0 translate-y-2">
                @svg('lucide-loader-circle', 'w-4 h-4 animate-spin')
                {{ __('platform::public.registering') }}
            </span>
        </button>

    </form>

    {{-- Divider --}}
    <div class="flex items-center gap-3 my-6">
        <div class="flex-1 h-px" style="background: var(--auth-divider);"></div>
        <span class="text-xs" style="color: var(--ui-content-subtle);">{{ __('platform::public.have_account') }}</span>
        <div class="flex-1 h-px" style="background: var(--auth-divider);"></div>
    </div>

    {{-- Link login --}}
    <a href="{{ route('login') }}" wire:navigate
       class="flex items-center justify-center w-full py-3 rounded-xl text-sm font-medium
              transition-all duration-200"
       style="color: var(--ui-content-muted); background: var(--auth-link-bg); border: 1px solid var(--auth-link-border);"
       onmouseover="this.style.background='var(--auth-link-bg-hover)';this.style.borderColor='var(--auth-link-border-hover)';this.style.color='var(--ui-content)';"
       onmouseout="this.style.background='var(--auth-link-bg)';this.style.borderColor='var(--auth-link-border)';this.style.color='var(--ui-content-muted)';">
        {{ __('platform::public.login_link') }}
    </a>
</div>
