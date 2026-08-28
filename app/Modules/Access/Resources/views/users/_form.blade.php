<div>
    <form wire:submit="save">
        <div class="space-y-6">

            {{-- Datos del usuario --}}
            <x-ts-card>

                {{-- Foto de perfil --}}
                <div class="mb-6 flex items-center gap-4">
                    <div class="relative">
                        @if ($photo)
                            <img
                                src="{{ $photo->temporaryUrl() }}"
                                alt="{{ __('platform::app.user_photo_preview') }}"
                                class="h-20 w-20 rounded-full object-cover ring-2 ring-line"
                            >
                        @elseif ($record?->profile?->photo_url)
                            <img
                                src="{{ $record->profile->photo_url }}"
                                alt="{{ __('platform::app.user_photo') }}"
                                class="h-20 w-20 rounded-full object-cover ring-2 ring-line"
                            >
                        @else
                            <div
                                class="flex h-20 w-20 items-center justify-center rounded-full text-2xl font-bold text-white"
                                style="background: linear-gradient(135deg, #f53003 0%, #c0392b 100%);"
                            >
                                {{ $record ? mb_strtoupper(mb_substr($record->name, 0, 1)) : '?' }}
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-content mb-1">{{ __('platform::app.user_photo') }}</label>
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
                        label="{{ __('platform::app.user_first_name') }}"
                        wire:model="form.first_name"
                        placeholder="{{ __('platform::app.user_first_name_ph') }}"
                    />
                    <x-ts-input
                        label="{{ __('platform::app.user_last_name') }}"
                        wire:model="form.last_name"
                        placeholder="{{ __('platform::app.user_last_name_ph') }}"
                    />
                    <x-ts-input
                        label="{{ __('platform::app.user_username') }}"
                        wire:model="form.username"
                        placeholder="{{ __('platform::app.user_username_ph') }}"
                        prefix="@"
                    />
                    <x-ts-input
                        label="{{ __('platform::app.user_email') }}"
                        type="email"
                        wire:model="form.email"
                        placeholder="{{ __('platform::app.user_email_ph') }}"
                    />
                    <x-ts-input
                        label="{{ $record ? __('platform::app.user_new_password') : __('platform::app.user_password') }}"
                        type="password"
                        wire:model="form.password"
                        placeholder="{{ __('platform::app.user_password_ph') }}"
                        hint="{{ $record ? __('platform::app.user_password_hint') : '' }}"
                    />
                    <x-ts-input
                        label="{{ __('platform::app.user_confirm') }}"
                        type="password"
                        wire:model="form.password_confirmation"
                        placeholder="{{ __('platform::app.user_confirm_ph') }}"
                    />
                    <x-ts-select.styled
                        label="{{ __('platform::app.user_role') }}"
                        wire:model.live="form.role"
                        :options="$roles"
                        option-label="label"
                        option-value="value"
                        placeholder="{{ __('platform::app.user_role_ph') }}"
                        :hint="$record ? __('platform::app.user_role_hint_edit') : __('platform::app.user_role_hint_new')"
                    />

                    @if ($record)
                        <div class="flex flex-col justify-center">
                            <x-ts-toggle
                                wire:model="form.is_active"
                                label="{{ __('platform::app.user_status_active') }}"
                                color="green"
                            />
                            <p class="mt-1 text-xs text-content-muted">{{ __('platform::app.user_status_hint') }}</p>
                        </div>
                    @endif
                </div>


            </x-ts-card>

            {{-- Permisos --}}
            <x-ts-card>
                <x-slot:header>
                    <div class="flex items-center justify-between py-3" style="padding-left: 14px; padding-right: 12px;">
                        <div>
                            <h3 class="text-sm font-semibold text-content">{{ __('platform::app.user_permissions') }}</h3>
                            <p class="text-xs text-content-muted">{{ __('platform::app.user_permissions_desc') }}</p>
                        </div>
                        @if ($record && $permissionList !== $originalPermissions)
                            <div class="group relative" x-data>
                                <button
                                    type="button"
                                    wire:click="restorePermissions"
                                    class="flex cursor-pointer items-center justify-center rounded-md border border-line bg-panel p-1.5 text-content-muted transition-colors hover:border-primary-400 hover:text-primary-600 dark:hover:border-primary-600 dark:hover:text-primary-400"
                                >
                                    @svg('lucide-rotate-ccw', 'size-3.5')
                                </button>
                                <div class="pointer-events-none absolute right-0 top-full mt-2 whitespace-nowrap rounded-md bg-gray-800 px-2 py-1 text-xs text-white opacity-0 transition-opacity duration-150 group-hover:opacity-100 dark:bg-dark-600 z-10">
                                    {{ __('platform::app.user_restore_perms') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </x-slot:header>

                {{-- Grid de permisos --}}
                <div class="space-y-6">
                    @foreach ($this->permissionsByGroup as $group => $modules)
                        <div>
                            {{-- Cabecera de grupo --}}
                            <div class="mb-3 flex items-center gap-3">
                                <span class="text-xs font-bold uppercase tracking-widest text-content-subtle">
                                    {{ __("access::roles.groups.{$group}") }}
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
                                                        @svg('lucide-check', 'size-2 text-white')
                                                    @endif
                                                </span>
                                                <span class="text-xs font-semibold tracking-wide text-primary-600 dark:text-primary-400">
                                                    {{ __("access::roles.modules.{$module}") }}
                                                </span>
                                            </button>
                                        </legend>

                                        <div class="grid gap-x-2 gap-y-0.5 pb-2 {{ count($permissions) > 1 ? 'grid-cols-2' : 'grid-cols-1' }}" style="margin-top: 6px;">
                                            @foreach ($permissions as $permission)
                                                @php
                                                    // El nombre del permiso lleva puntos (R40) y `__()` los
                                                    // lee como niveles de array, así que la búsqueda se hace
                                                    // sobre el array ya resuelto, donde el punto no significa
                                                    // nada.
                                                    $permLabel = __('access::roles.permissions')[$permission] ?? $permission;
                                                    $permDesc  = __('access::roles.descriptions')[$permission] ?? '';
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
                                                            @svg('lucide-check', 'size-2 text-white')
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
                        <a href="{{ route('access.users.index') }}" wire:navigate
                           class="text-sm text-content-muted hover:text-content">
                            {{ __('platform::app.cancel') }}
                        </a>
                        <x-ts-button type="submit" wire:loading.attr="disabled" sm>
                            {{ $record ? __('platform::app.user_btn_update') : __('platform::app.user_btn_create') }}
                        </x-ts-button>
                    </div>
                </x-slot:footer>

            </x-ts-card>

        </div>
    </form>
</div>
