<div>
    {{-- Header --}}
    <div class="mb-7 text-center">
        <h1 class="text-2xl font-bold mb-1.5" style="color: var(--ui-content);">{{ __('public.forgot_title') }}</h1>
        <p class="text-sm" style="color: var(--ui-content-muted);">{{ __('public.forgot_subtitle') }}</p>
    </div>

    @if ($linkSent)
        {{-- Confirmación de envío --}}
        <div class="rounded-xl p-4 mb-6 text-center"
             style="background: rgba(22,163,74,0.08); border: 1px solid rgba(22,163,74,0.2);">
            <div class="flex items-center justify-center gap-2 mb-1">
                @svg('lucide-mail-check', 'w-5 h-5 text-success')
                <span class="text-sm font-semibold text-success">{{ __('passwords.sent') }}</span>
            </div>
            <p class="text-xs mt-1" style="color: var(--ui-content-muted);">{{ $email }}</p>
        </div>

        {{-- Reenviar --}}
        <button type="button"
                wire:click="sendResetLink"
                wire:loading.attr="disabled"
                class="w-full h-12 rounded-xl text-sm font-semibold text-white
                       relative overflow-hidden transition-all duration-200 cursor-pointer"
                style="background:linear-gradient(135deg,#f53003 0%,#c0392b 100%);
                       box-shadow:0 4px 20px rgba(245,48,3,0.35);"
                onmouseover="this.style.boxShadow='0 8px 28px rgba(245,48,3,0.52)';this.style.transform='translateY(-1px)';"
                onmouseout="this.style.boxShadow='0 4px 20px rgba(245,48,3,0.35)';this.style.transform='translateY(0)';">
            <span wire:loading.class="opacity-0 -translate-y-2"
                  class="absolute inset-0 flex items-center justify-center transition-all duration-200 ease-in-out">
                {{ __('public.forgot_button') }}
            </span>
            <span wire:loading.class.remove="opacity-0 translate-y-2"
                  class="absolute inset-0 flex items-center justify-center gap-2 transition-all duration-200 ease-in-out opacity-0 translate-y-2">
                @svg('lucide-loader-circle', 'w-4 h-4 animate-spin')
                {{ __('public.forgot_sending') }}
            </span>
        </button>
    @else
        <form wire:submit="sendResetLink" class="flex flex-col gap-4" novalidate>

            {{-- Email --}}
            <x-ui.auth-input
                wire:model="email"
                type="email"
                label="{{ __('public.email') }}"
                placeholder="tu@email.com"
                autocomplete="email"
            />

            {{-- Submit --}}
            <button type="submit"
                    wire:loading.attr="disabled"
                    class="w-full h-12 rounded-xl text-sm font-semibold text-white mt-1
                           relative overflow-hidden transition-all duration-200 cursor-pointer"
                    style="background:linear-gradient(135deg,#f53003 0%,#c0392b 100%);
                           box-shadow:0 4px 20px rgba(245,48,3,0.35);"
                    onmouseover="this.style.boxShadow='0 8px 28px rgba(245,48,3,0.52)';this.style.transform='translateY(-1px)';"
                    onmouseout="this.style.boxShadow='0 4px 20px rgba(245,48,3,0.35)';this.style.transform='translateY(0)';">
                <span wire:loading.class="opacity-0 -translate-y-2"
                      class="absolute inset-0 flex items-center justify-center transition-all duration-200 ease-in-out">
                    {{ __('public.forgot_button') }}
                </span>
                <span wire:loading.class.remove="opacity-0 translate-y-2"
                      class="absolute inset-0 flex items-center justify-center gap-2 transition-all duration-200 ease-in-out opacity-0 translate-y-2">
                    @svg('lucide-loader-circle', 'w-4 h-4 animate-spin')
                    {{ __('public.forgot_sending') }}
                </span>
            </button>

        </form>
    @endif

    {{-- Divider --}}
    <div class="flex items-center gap-3 my-6">
        <div class="flex-1 h-px" style="background: var(--auth-divider);"></div>
        <div class="flex-1 h-px" style="background: var(--auth-divider);"></div>
    </div>

    {{-- Volver a login --}}
    <a href="{{ route('login') }}" wire:navigate
       class="flex items-center justify-center gap-2 w-full py-3 rounded-xl text-sm font-medium
              transition-all duration-200"
       style="color: var(--ui-content-muted); background: var(--auth-link-bg); border: 1px solid var(--auth-link-border);"
       onmouseover="this.style.background='var(--auth-link-bg-hover)';this.style.borderColor='var(--auth-link-border-hover)';this.style.color='var(--ui-content)';"
       onmouseout="this.style.background='var(--auth-link-bg)';this.style.borderColor='var(--auth-link-border)';this.style.color='var(--ui-content-muted)';">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        {{ __('public.forgot_back_login') }}
    </a>
</div>
