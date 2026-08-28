<div>
    <form wire:submit="save" x-on:submit="sessionStorage.setItem('restoreScroll', window.scrollY)">
        <x-ts-select.styled
            label="{{ __('platform::settings.language_label') }}"
            wire:model.live="locale"
            :options="$languages"
            option-label="label"
            option-value="value"
            hint="{{ __('platform::settings.language_hint') }}"
            searchable
        />

        <div class="mt-4 flex justify-end">
            <x-ts-button type="submit" wire:loading.attr="disabled" sm>
                {{ __('platform::settings.save_language') }}
            </x-ts-button>
        </div>
    </form>
</div>
