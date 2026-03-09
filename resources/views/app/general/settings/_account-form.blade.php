<div>
    <form wire:submit="save">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <x-ts-input
                label="{{ __('settings.username') }}"
                wire:model="username"
                placeholder="Ej. juan_garcia"
                prefix="@"
            />
            <x-ts-input
                label="{{ __('settings.email') }}"
                type="email"
                wire:model="email"
                placeholder="correo@ejemplo.com"
            />
        </div>

        <div class="mt-4 flex justify-end">
            <x-ts-button type="submit" wire:loading.attr="disabled" sm>
                {{ __('settings.save_changes') }}
            </x-ts-button>
        </div>
    </form>
</div>
