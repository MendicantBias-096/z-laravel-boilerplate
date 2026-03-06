<div>
    <x-ts-card>

        {{-- Estado: desactivado --}}
        @if (! $enabled && ! $pending)
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    @svg('lucide-shield-off', 'size-5 text-content-subtle shrink-0')
                    <span class="text-sm text-content-muted">{{ __('settings.two_factor_not_enabled') }}</span>
                </div>
                <x-ts-button wire:click="enable" wire:loading.attr="disabled" sm>
                    {{ __('settings.two_factor_enable') }}
                </x-ts-button>
            </div>
        @endif

        {{-- Estado: pendiente de confirmar (QR visible) --}}
        @if ($pending)
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    @svg('lucide-scan-qr-code', 'size-5 text-amber-500 shrink-0')
                    <span class="text-sm font-medium text-content">{{ __('settings.two_factor_scan') }}</span>
                </div>

                @if ($qrCodeSvg)
                    <div class="flex justify-center">
                        <div class="rounded-xl border border-line bg-white p-4 inline-block">
                            {!! $qrCodeSvg !!}
                        </div>
                    </div>
                @endif

                <p class="text-xs text-content-muted">{{ __('settings.two_factor_qr_instruction') }}</p>

                <div class="flex gap-3">
                    <div class="flex-1">
                        <x-ts-input
                            wire:model="confirmationCode"
                            label="{{ __('settings.two_factor_confirm_code') }}"
                            placeholder="000000"
                            inputmode="numeric"
                        />
                    </div>
                    <div class="flex items-end gap-2">
                        <x-ts-button wire:click="confirm" wire:loading.attr="disabled" sm>
                            {{ __('settings.two_factor_confirm') }}
                        </x-ts-button>
                        <x-ts-button wire:click="disable" wire:loading.attr="disabled" sm color="red">
                            {{ __('settings.cancel') }}
                        </x-ts-button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Estado: activado --}}
        @if ($enabled)
            <div class="space-y-4">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        @svg('lucide-shield-check', 'size-5 text-green-500 shrink-0')
                        <span class="text-sm font-medium text-content">{{ __('settings.two_factor_active') }}</span>
                    </div>
                    <x-ts-button wire:click="disable" wire:loading.attr="disabled" sm color="red">
                        {{ __('settings.two_factor_disable') }}
                    </x-ts-button>
                </div>

                <div class="flex gap-2">
                    <x-ts-button wire:click="showRecoveryCodes" wire:loading.attr="disabled" sm color="secondary">
                        {{ $showingRecovery ? __('settings.recovery_codes_hide') : __('settings.recovery_codes_show') }}
                    </x-ts-button>
                    <x-ts-button wire:click="regenerateCodes" wire:loading.attr="disabled" sm color="secondary">
                        {{ __('settings.regenerate_codes') }}
                    </x-ts-button>
                </div>

                @if ($showingRecovery && count($recoveryCodes))
                    <div class="rounded-lg border border-line bg-panel-alt p-4">
                        <p class="mb-3 text-xs text-content-muted">{{ __('settings.recovery_codes_desc') }}</p>
                        <div class="grid grid-cols-2 gap-1">
                            @foreach ($recoveryCodes as $code)
                                <code class="font-mono text-xs text-content">{{ $code }}</code>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif

    </x-ts-card>
</div>
