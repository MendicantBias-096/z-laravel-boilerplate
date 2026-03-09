<div>
    <form wire:submit="save">
        {{-- Logo + Favicon --}}
        <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">

            {{-- Logo --}}
            <div class="flex items-center gap-4 min-w-0">
                <div class="shrink-0">
                    @if ($logo)
                        <img
                            src="{{ $logo->temporaryUrl() }}"
                            alt="Vista previa"
                            class="h-16 w-16 rounded-xl object-cover ring-2 ring-line"
                        >
                    @elseif (\App\Models\Setting::get('logo_path'))
                        <img
                            src="{{ Storage::disk('public')->url(\App\Models\Setting::get('logo_path')) }}"
                            alt="Logo"
                            class="h-16 w-16 rounded-xl object-cover ring-2 ring-line"
                        >
                    @else
                        <div
                            class="flex h-16 w-16 items-center justify-center rounded-xl text-xl font-bold text-white"
                            style="background: linear-gradient(135deg, #f53003 0%, #c0392b 100%);"
                        >
                            {{ mb_strtoupper(mb_substr($app_name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="min-w-0">
                    <label class="block text-sm font-medium text-content mb-1">Logo</label>
                    <input
                        type="file"
                        wire:model="logo"
                        accept="image/png,image/jpeg,image/svg+xml,image/webp"
                        class="block w-full text-sm text-content-subtle file:mr-3 file:cursor-pointer file:rounded-md file:border file:border-line file:bg-panel-alt file:px-3 file:py-1.5 file:text-sm file:text-content hover:file:bg-panel"
                    >
                    <p class="mt-1 text-xs text-content-muted">PNG, JPG, SVG o WebP. Máx. 2 MB.</p>
                    @error('logo')
                        <p class="mt-1 text-xs text-danger">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Favicon --}}
            <div class="flex items-center gap-4 min-w-0">
                <div class="shrink-0">
                    @if ($favicon)
                        <img
                            src="{{ $favicon->temporaryUrl() }}"
                            alt="Vista previa"
                            class="h-16 w-16 rounded-xl object-cover ring-2 ring-line"
                        >
                    @elseif (\App\Models\Setting::get('favicon_path'))
                        <img
                            src="{{ Storage::disk('public')->url(\App\Models\Setting::get('favicon_path')) }}"
                            alt="Favicon"
                            class="h-16 w-16 rounded-xl object-cover ring-2 ring-line"
                        >
                    @else
                        <div class="flex h-16 w-16 items-center justify-center rounded-xl border-2 border-dashed border-line bg-panel-alt">
                            @svg('lucide-link', 'size-6 text-content-subtle')
                        </div>
                    @endif
                </div>
                <div class="min-w-0">
                    <label class="block text-sm font-medium text-content mb-1">Favicon</label>
                    <input
                        type="file"
                        wire:model="favicon"
                        accept="image/png,image/jpeg,image/svg+xml,image/webp,image/x-icon"
                        class="block w-full text-sm text-content-subtle file:mr-3 file:cursor-pointer file:rounded-md file:border file:border-line file:bg-panel-alt file:px-3 file:py-1.5 file:text-sm file:text-content hover:file:bg-panel"
                    >
                    <p class="mt-1 text-xs text-content-muted">PNG, SVG, WebP o ICO. Máx. 512 KB.</p>
                    @error('favicon')
                        <p class="mt-1 text-xs text-danger">{{ $message }}</p>
                    @enderror
                </div>
            </div>

        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            <x-ts-input
                label="Nombre de la aplicación"
                wire:model="app_name"
                placeholder="Ej. Mi Sistema"
            />
            <x-ts-input
                label="Nombre corto (sidebar)"
                wire:model="app_short_name"
                placeholder="Ej. MiSys"
                hint="Máx. 20 caracteres. Si se deja vacío se trunca el nombre principal."
            />
        </div>

        {{-- Nota técnica sutil --}}
        <details class="mt-4 group">
            <summary class="flex cursor-pointer items-center gap-1.5 text-xs text-content-subtle select-none hover:text-content-muted transition-colors">
                @svg('lucide-info', 'size-3.5 shrink-0')
                ¿El nombre no aparece en la pestaña del navegador?
            </summary>
            <p class="mt-2 text-xs text-content-subtle leading-relaxed pl-5">
                Un técnico de TI puede resolver esto configurando la variable <code class="rounded bg-panel-alt px-1 font-mono">APP_NAME</code> en el servidor para que los logs y servicios del sistema queden alineados.
            </p>
        </details>

        <div class="mt-4 flex justify-end">
            <x-ts-button type="submit" wire:loading.attr="disabled" sm>
                Guardar cambios
            </x-ts-button>
        </div>
    </form>
</div>
