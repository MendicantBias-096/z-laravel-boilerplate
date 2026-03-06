<div>
    <x-ts-card>
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                @if (auth()->user()->hasVerifiedEmail())
                    @svg('lucide-circle-check', 'size-5 text-green-500 shrink-0')
                    <span class="text-sm font-medium text-content">{{ __('settings.email_verified') }}</span>
                @else
                    @svg('lucide-circle-alert', 'size-5 text-amber-500 shrink-0')
                    <span class="text-sm font-medium text-content">{{ __('settings.email_not_verified') }}</span>
                @endif
            </div>

            @if (! auth()->user()->hasVerifiedEmail())
                <x-ts-button wire:click="resend" wire:loading.attr="disabled" sm color="amber">
                    {{ $sent ? __('settings.verification_sent_short') : __('settings.resend_verification') }}
                </x-ts-button>
            @endif
        </div>

        <div class="mt-3">
            <p class="text-xs text-content-muted">{{ auth()->user()->email }}</p>
        </div>
    </x-ts-card>
</div>
