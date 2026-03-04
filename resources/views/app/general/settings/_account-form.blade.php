<div>
    <form wire:submit="save">
        <x-ts-card>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-ts-input
                    label="Usuario"
                    wire:model="username"
                    placeholder="Ej. juan_garcia"
                    prefix="@"
                />
                <x-ts-input
                    label="Correo electrónico"
                    type="email"
                    wire:model="email"
                    placeholder="correo@ejemplo.com"
                />
            </div>

            <x-slot:footer>
                <div class="flex justify-end">
                    <x-ts-button type="submit" wire:loading.attr="disabled" sm>
                        Guardar cambios
                    </x-ts-button>
                </div>
            </x-slot:footer>
        </x-ts-card>
    </form>
</div>
