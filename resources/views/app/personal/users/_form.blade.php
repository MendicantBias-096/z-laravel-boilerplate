<div>
    <form wire:submit="save">
        <x-ts-card>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-ts-input
                    label="Nombre"
                    wire:model="form.first_name"
                    placeholder="Ej. Juan"
                />
                <x-ts-input
                    label="Apellido"
                    wire:model="form.last_name"
                    placeholder="Ej. García"
                />
                <x-ts-input
                    label="Usuario"
                    wire:model="form.username"
                    placeholder="Ej. juan_garcia"
                    prefix="@"
                />
                <x-ts-input
                    label="Correo electrónico"
                    type="email"
                    wire:model="form.email"
                    placeholder="correo@ejemplo.com"
                />
                <x-ts-input
                    label="{{ $record ? 'Nueva contraseña' : 'Contraseña' }}"
                    type="password"
                    wire:model="form.password"
                    placeholder="Mínimo 8 caracteres"
                    hint="{{ $record ? 'Dejar vacío para mantener la actual' : '' }}"
                />
                <x-ts-input
                    label="Confirmar contraseña"
                    type="password"
                    wire:model="form.password_confirmation"
                    placeholder="Repite la contraseña"
                />
                <x-ts-select.styled
                    label="Rol"
                    wire:model="form.role"
                    :options="collect($roles)->map(fn ($label, $value) => ['label' => ucfirst($label), 'value' => $value])->values()->toArray()"
                    option-label="label"
                    option-value="value"
                    placeholder="Selecciona un rol"
                />
            </div>

            <x-slot:footer>
                <div class="flex items-center justify-end gap-2">
                    <a href="{{ route('personal.usuarios.index') }}" wire:navigate
                       class="text-sm text-content-muted hover:text-content">
                        Cancelar
                    </a>
                    <x-ts-button type="submit" wire:loading.attr="disabled" sm>
                        {{ $record ? 'Actualizar' : 'Crear usuario' }}
                    </x-ts-button>
                </div>
            </x-slot:footer>

        </x-ts-card>
    </form>
</div>
