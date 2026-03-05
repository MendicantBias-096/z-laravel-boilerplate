<div>
    <form wire:submit="save">
        <x-ts-card>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-ts-input
                    label="{{ __('settings.current_password') }}"
                    type="password"
                    wire:model="current_password"
                    class="sm:col-span-2"
                />
                <x-ts-input
                    label="{{ __('settings.new_password') }}"
                    type="password"
                    wire:model="password"
                />
                <x-ts-input
                    label="{{ __('settings.confirm_new_password') }}"
                    type="password"
                    wire:model="password_confirmation"
                />
            </div>

            <x-slot:footer>
                <div class="flex justify-end">
                    <x-ts-button type="submit" wire:loading.attr="disabled" sm>
                        {{ __('settings.change_password') }}
                    </x-ts-button>
                </div>
            </x-slot:footer>
        </x-ts-card>
    </form>
</div>
