<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-content">
            {{ $record ? 'Editar usuario' : 'Nuevo usuario' }}
        </h1>
    </div>

    <form wire:submit="save">
        <x-ts-card>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                <x-ts-input
                    label="Nombre"
                    wire:model="form.name"
                    placeholder="Nombre completo"
                />

                <x-ts-input
                    label="Correo electrónico"
                    type="email"
                    wire:model="form.email"
                    placeholder="correo@ejemplo.com"
                />

                <x-ts-input
                    label="{{ $record ? 'Nueva contraseña (dejar vacío para no cambiar)' : 'Contraseña' }}"
                    type="password"
                    wire:model="form.password"
                    placeholder="Mínimo 8 caracteres"
                />

                <x-ts-input
                    label="Confirmar contraseña"
                    type="password"
                    wire:model="form.password_confirmation"
                    placeholder="Repite la contraseña"
                />

                <x-ts-select
                    label="Rol"
                    wire:model="form.role"
                    :options="collect($roles)->map(fn ($label, $value) => ['label' => ucfirst($label), 'value' => $value])->values()->toArray()"
                    option-label="label"
                    option-value="value"
                    placeholder="Selecciona un rol"
                />

            </div>

            <x-slot:footer>
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('personal.usuarios.index') }}" wire:navigate
                       class="text-sm text-content-muted hover:text-content">
                        Cancelar
                    </a>
                    <x-ts-button type="submit" wire:loading.attr="disabled">
                        Guardar
                    </x-ts-button>
                </div>
            </x-slot:footer>
        </x-ts-card>
    </form>
</div>
