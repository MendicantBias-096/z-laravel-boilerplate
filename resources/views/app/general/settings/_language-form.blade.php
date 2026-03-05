<div>
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
                    <x-ts-button type="submit" wire:loading.attr="disabled" sm>
                        {{ __('settings.save_language') }}
                    </x-ts-button>
                </div>
            </x-slot:footer>
        </x-ts-card>
    </form>
</div>
