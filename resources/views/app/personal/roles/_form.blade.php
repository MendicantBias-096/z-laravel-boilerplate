<div>
    <form wire:submit="save">
        <div class="space-y-6">

            {{-- ── Datos del rol ─────────────────────────────────────────────── --}}
            <x-ts-card>
                <x-ts-input
                    label="Nombre del rol"
                    wire:model.live="display_name"
                    placeholder="Ej. Administrador de CEDIS"
                    hint="Escribe el nombre como se mostrará en la interfaz."
                />

                @if ($name)
                    <div class="mt-2 flex items-center gap-2 text-xs text-content-subtle">
                        <span>Identificador:</span>
                        <code class="rounded bg-panel-alt px-2 py-0.5 font-mono text-content-muted">{{ $name }}</code>
                    </div>
                @endif

            </x-ts-card>

            {{-- ── Permisos ───────────────────────────────────────────────────── --}}
            <x-ts-card>
                <x-slot:header>
                    <div class="py-3" style="padding-left: 14px;">
                        <h3 class="text-sm font-semibold text-content">Permisos del rol</h3>
                        <p class="text-xs text-content-muted">Selecciona los accesos que tendrá este rol.</p>
                    </div>
                </x-slot:header>

                <div class="space-y-6">
                    @foreach ($this->permissionsByGroup as $group => $modules)
                        <div>
                            {{-- Cabecera de grupo --}}
                            <div class="mb-3 flex items-center gap-3">
                                <span class="text-xs font-bold uppercase tracking-widest text-content-subtle">
                                    {{ __("roles.groups.{$group}") }}
                                </span>
                                <div class="h-px flex-1 bg-line"></div>
                            </div>

                            {{-- Módulos del grupo --}}
                            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                                @foreach ($modules as $module => $permissions)
                                    @php $allSelected = $this->moduleFullySelected($module); @endphp

                                    <fieldset class="rounded-lg border border-primary-400/40 dark:border-primary-600/40 px-4 pt-3">
                                        <legend class="-ml-1 px-2">
                                            <button
                                                type="button"
                                                wire:click="toggleModule('{{ $module }}')"
                                                class="flex cursor-pointer items-center gap-1.5 rounded px-1 py-0.5"
                                            >
                                                <span @class([
                                                    'flex size-3.5 shrink-0 items-center justify-center rounded border transition-colors',
                                                    'border-primary-500 bg-primary-500' => $allSelected,
                                                    'border-line bg-panel' => !$allSelected,
                                                ])>
                                                    @if ($allSelected)
                                                        <x-ui.icon name="check" class="size-2 text-white" />
                                                    @endif
                                                </span>
                                                <span class="text-xs font-semibold tracking-wide text-primary-600 dark:text-primary-400">
                                                    {{ __("roles.modules.{$module}") }}
                                                </span>
                                            </button>
                                        </legend>

                                        <div class="grid gap-x-2 gap-y-0.5 pb-2 {{ count($permissions) > 1 ? 'grid-cols-2' : 'grid-cols-1' }}">
                                            @foreach ($permissions as $permission)
                                                @php
                                                    $permLabel = __("roles.permissions.{$permission}");
                                                    $permDesc  = __("roles.descriptions.{$permission}");
                                                @endphp
                                                <label
                                                    class="flex cursor-pointer items-center gap-2 rounded px-2 py-1 mt-[4px] ml-1 transition-colors hover:bg-primary-50 dark:hover:bg-primary-950/30"
                                                    x-data="{
                                                        show: false,
                                                        pos: { top: 0, left: 0 },
                                                        open(el) {
                                                            const r = el.getBoundingClientRect();
                                                            this.pos = { top: r.top - 8, left: r.left + r.width / 2 };
                                                            this.show = true;
                                                        }
                                                    }"
                                                    @mouseenter="open($el)"
                                                    @mouseleave="show = false"
                                                >
                                                    <span class="relative size-3.5 shrink-0">
                                                        <input
                                                            type="checkbox"
                                                            wire:model.live="permissionList"
                                                            value="{{ $permission }}"
                                                            class="peer sr-only"
                                                        />
                                                        <span class="absolute inset-0 rounded border border-line bg-panel transition-colors peer-checked:border-primary-500 peer-checked:bg-primary-500"></span>
                                                        <span class="absolute inset-0 flex items-center justify-center opacity-0 transition-opacity peer-checked:opacity-100">
                                                            <x-ui.icon name="check" class="size-2 text-white" />
                                                        </span>
                                                    </span>
                                                    <span class="text-xs font-semibold text-content-muted leading-tight line-clamp-2">{{ $permLabel }}</span>

                                                    <template x-teleport="#app-root">
                                                        <div
                                                            x-show="show"
                                                            x-cloak
                                                            x-transition:enter="transition ease-out duration-100"
                                                            x-transition:enter-start="opacity-0 translate-y-1"
                                                            x-transition:enter-end="opacity-100 translate-y-0"
                                                            :style="`position:fixed; top:${pos.top}px; left:${pos.left}px; transform:translate(-50%,-100%); z-index:9999;`"
                                                            class="w-72 rounded-lg border border-line bg-panel px-3 py-2 text-sm text-content-muted shadow-lg"
                                                        >
                                                            {{ $permDesc }}
                                                        </div>
                                                    </template>
                                                </label>
                                            @endforeach
                                        </div>
                                    </fieldset>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <x-slot:footer>
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('personal.roles.index') }}" wire:navigate
                           class="text-sm text-content-muted hover:text-content">
                            Cancelar
                        </a>
                        <x-ts-button type="submit" wire:loading.attr="disabled" sm>
                            Guardar
                        </x-ts-button>
                    </div>
                </x-slot:footer>
            </x-ts-card>

        </div>


    </form>
</div>
