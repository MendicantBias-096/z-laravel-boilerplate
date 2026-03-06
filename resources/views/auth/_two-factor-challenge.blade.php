<div>
    {{-- Header --}}
    <div class="mb-7 text-center">
        <div class="mx-auto mb-4 flex size-14 items-center justify-center rounded-full"
             style="background: rgba(245,48,3,0.1);">
            @svg('lucide-shield-check', 'size-7', ['style' => 'color:#f53003;'])
        </div>
        <h1 class="text-2xl font-bold mb-1.5" style="color: var(--ui-content);">{{ __('public.two_factor_title') }}</h1>
        <p class="text-sm" style="color: var(--ui-content-muted);">
            {{ $showRecovery ? __('public.two_factor_recovery_subtitle') : __('public.two_factor_subtitle') }}
        </p>
    </div>

    <form method="POST" action="{{ route('two-factor.login') }}" class="flex flex-col gap-4">
        @csrf

        @if (! $showRecovery)
            <x-ui.auth-input
                name="code"
                wire:model="code"
                label="{{ __('public.two_factor_code') }}"
                placeholder="000000"
                autocomplete="one-time-code"
                inputmode="numeric"
            />
        @else
            <x-ui.auth-input
                name="recovery_code"
                wire:model="recovery_code"
                label="{{ __('public.two_factor_recovery_code') }}"
                placeholder="xxxxx-xxxxx"
                autocomplete="one-time-code"
            />
        @endif

        <button type="submit"
                class="w-full h-12 rounded-xl text-sm font-semibold text-white relative overflow-hidden transition-all duration-200"
                style="background:linear-gradient(135deg,#f53003 0%,#c0392b 100%);
                       box-shadow:0 4px 20px rgba(245,48,3,0.35);"
                onmouseover="this.style.boxShadow='0 8px 28px rgba(245,48,3,0.52)';this.style.transform='translateY(-1px)';"
                onmouseout="this.style.boxShadow='0 4px 20px rgba(245,48,3,0.35)';this.style.transform='translateY(0)';">
            {{ __('public.verify') }}
        </button>
    </form>

    <div class="mt-6 text-center">
        <button wire:click="$set('showRecovery', {{ $showRecovery ? 'false' : 'true' }})"
                type="button"
                class="text-sm transition-colors duration-200"
                style="color: var(--ui-content-subtle);"
                onmouseover="this.style.color='var(--ui-content-muted)';"
                onmouseout="this.style.color='var(--ui-content-subtle)';">
            {{ $showRecovery ? __('public.use_auth_code') : __('public.use_recovery_code') }}
        </button>
    </div>
</div>
