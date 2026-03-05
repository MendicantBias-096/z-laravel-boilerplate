<div>
    <form wire:submit="save">
        <x-ts-card>
            {{-- Foto de perfil --}}
            <div class="mb-6 flex items-center gap-4">
                <div class="relative">
                    @if ($photo)
                        <img
                            src="{{ $photo->temporaryUrl() }}"
                            alt="Vista previa"
                            class="h-20 w-20 rounded-full object-cover ring-2 ring-line"
                        >
                    @elseif (auth()->user()->profile?->photo_url)
                        <img
                            src="{{ auth()->user()->profile->photo_url }}"
                            alt="Foto de perfil"
                            class="h-20 w-20 rounded-full object-cover ring-2 ring-line"
                        >
                    @else
                        <div
                            class="flex h-20 w-20 items-center justify-center rounded-full text-2xl font-bold text-white"
                            style="background: linear-gradient(135deg, #f53003 0%, #c0392b 100%);"
                        >
                            {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-content mb-1">Foto de perfil</label>
                    <input
                        type="file"
                        wire:model="photo"
                        accept="image/*"
                        class="block text-sm text-content-subtle file:mr-3 file:cursor-pointer file:rounded-md file:border file:border-line file:bg-panel-alt file:px-3 file:py-1.5 file:text-sm file:text-content hover:file:bg-panel"
                    >
                    @error('photo')
                        <p class="mt-1 text-xs text-danger">{{ $message }}</p>
                    @enderror
                </div>
            </div>

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
                <x-ts-select.native
                    label="Idioma"
                    wire:model="locale"
                    hint="Afecta el idioma de las vistas públicas (login, registro)."
                    :options="[
                        ['label' => 'Español', 'value' => 'es'],
                        ['label' => 'English', 'value' => 'en'],
                    ]"
                    option-label="label"
                    option-value="value"
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
