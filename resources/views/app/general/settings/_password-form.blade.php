<div>
    <form wire:submit="save">
        <x-ts-card>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-ts-input
                    label="Contraseña actual"
                    type="password"
                    wire:model="current_password"
                    placeholder="Tu contraseña actual"
                    class="sm:col-span-2"
                />
                <x-ts-input
                    label="Nueva contraseña"
                    type="password"
                    wire:model="password"
                    placeholder="Mínimo 8 caracteres"
                />
                <x-ts-input
                    label="Confirmar nueva contraseña"
                    type="password"
                    wire:model="password_confirmation"
                    placeholder="Repite la nueva contraseña"
                />
            </div>

            <x-slot:footer>
                <div class="flex justify-end">
                    <x-ts-button type="submit" wire:loading.attr="disabled" sm>
                        Cambiar contraseña
                    </x-ts-button>
                </div>
            </x-slot:footer>
        </x-ts-card>
    </form>
</div>
