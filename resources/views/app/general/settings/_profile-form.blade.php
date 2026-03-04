<div>
    <x-ts-card>
        <form wire:submit="save">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-ts-input
                    label="Nombre"
                    wire:model="first_name"
                    placeholder="Ej. Juan"
                />
                <x-ts-input
                    label="Apellido"
                    wire:model="last_name"
                    placeholder="Ej. García"
                />
            </div>

            <x-slot:footer>
                <div class="flex justify-end">
                    <x-ts-button type="submit" wire:loading.attr="disabled" sm>
                        Guardar cambios
                    </x-ts-button>
                </div>
            </x-slot:footer>
        </form>
    </x-ts-card>
</div>
