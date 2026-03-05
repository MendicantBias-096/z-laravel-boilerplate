<div>
    {{-- Overlay de carga durante el cambio de idioma --}}
    <div
        wire:loading.flex
        wire:target="save"
        class="fixed inset-0 z-50 items-center justify-center bg-white/75 backdrop-blur-sm dark:bg-gray-950/75"
    >
        <div class="flex flex-col items-center gap-3">
            <x-ui.icon name="loader-circle" class="size-8 animate-spin text-primary-500" />
            <span class="text-sm font-medium text-content-muted">{{ __('settings.language_label') }}...</span>
        </div>
    </div>

    <form wire:submit="save">
        <x-ts-card>
            <x-ts-select.native
                label="{{ __('settings.language_label') }}"
                wire:model="locale"
                :options="[
                    ['label' => 'Español', 'value' => 'es'],
                    ['label' => 'English', 'value' => 'en'],
                ]"
                option-label="label"
                option-value="value"
                hint="{{ __('settings.language_hint') }}"
            />

            <x-slot:footer>
                <div class="flex justify-end">
                    <x-ts-button type="submit" wire:loading.attr="disabled" wire:target="save" sm>
                        {{ __('settings.save_language') }}
                    </x-ts-button>
                </div>
            </x-slot:footer>
        </x-ts-card>
    </form>
</div>
