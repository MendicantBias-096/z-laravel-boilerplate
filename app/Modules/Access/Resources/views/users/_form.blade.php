<div>
    {{--
        El chasis de formulario del proyecto: menú de secciones a la izquierda,
        cuerpo con scroll propio a la derecha y pie anclado con los botones.

        La caja mide siempre lo mismo, así que cambiar de sección no reacomoda
        la página —Identidad tiene cuatro campos y Accesos ochenta— y «Guardar»
        no se va nunca fuera de la vista.
    --}}
    <div class="form-card grid grid-cols-1 overflow-hidden rounded-lg border border-line lg:grid-cols-[13rem_minmax(0,1fr)]">

        <x-ui.form-rail
            :sections="$this->sections"
            :section="$section"
            :dirty="$this->dirtySections"
            :title="__('platform::app.user_nav_title')"
        >
            @if ($record)
                <x-slot:pie>
                    <dl class="space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <dt class="text-content-muted">{{ __('platform::app.user_status_active') }}</dt>
                            <dd class="flex items-center gap-1.5 font-medium {{ $record->is_active ? 'text-success' : 'text-content-muted' }}">
                                <span class="size-1.5 rounded-full {{ $record->is_active ? 'bg-success' : 'bg-content-muted' }}"></span>
                                {{ $record->is_active ? __('platform::app.user_status_active') : __('platform::app.user_status_inactive') }}
                            </dd>
                        </div>
                    </dl>
                </x-slot:pie>
            @endif
        </x-ui.form-rail>

        <div wire:key="section-{{ $section }}" class="section-panel flex min-h-0 min-w-0 flex-col">
        <x-ui.form-shell
            action="save"
            :title="$record ? $record->username : __('platform::app.user_btn_create')"
            :description="__('platform::app.user_form_hint')"
            :icon="$this->sections[array_search($section, array_column($this->sections, 'key'), true) ?: 0]['icon']"
        >

            @if ($section === 'identity')
                <x-ui.form-section
                    :title="__('platform::app.user_section_identity')"
                    :description="__('platform::app.user_photo')"
                >
                    <div class="flex items-center gap-4">
                        @if ($photo)
                            <img
                                src="{{ $photo->temporaryUrl() }}"
                                alt="{{ __('platform::app.user_photo_preview') }}"
                                class="size-20 shrink-0 rounded-full object-cover ring-2 ring-line"
                            >
                        @elseif ($record?->profile?->photo_url)
                            <img
                                src="{{ $record->profile->photo_url }}"
                                alt="{{ __('platform::app.user_photo') }}"
                                class="size-20 shrink-0 rounded-full object-cover ring-2 ring-line"
                            >
                        @else
                            <div class="flex size-20 shrink-0 items-center justify-center rounded-full bg-primary-500 text-2xl font-bold text-white">
                                {{ $record ? mb_strtoupper(mb_substr($record->name, 0, 1)) : '?' }}
                            </div>
                        @endif

                        <div class="min-w-0">
                            <label class="mb-1 block text-sm font-medium text-content">{{ __('platform::app.user_photo') }}</label>
                            <input
                                type="file"
                                wire:model="photo"
                                accept="image/*"
                                class="block w-full text-sm text-content-subtle file:mr-3 file:cursor-pointer file:rounded-md file:border file:border-line file:bg-panel-alt file:px-3 file:py-1.5 file:text-sm file:text-content hover:file:bg-panel"
                            >
                            @error('photo')
                                <p class="mt-1 text-xs text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
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
                            class="sm:col-span-2"
                        />
                    </div>
                </x-ui.form-section>
            @endif

            @if ($section === 'account')
                <x-ui.form-section
                    :title="__('platform::app.user_section_account')"
                    :description="__('platform::app.user_password_hint')"
                >
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <x-ts-input
                            label="{{ __('platform::app.user_email') }}"
                            type="email"
                            wire:model="form.email"
                            placeholder="{{ __('platform::app.user_email_ph') }}"
                            class="sm:col-span-2"
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
                    </div>

                    @if ($record)
                        <div class="mt-4">
                            <x-ts-toggle
                                wire:model="form.is_active"
                                label="{{ __('platform::app.user_status_active') }}"
                                color="green"
                            />
                            <p class="mt-1 text-xs text-content-muted">{{ __('platform::app.user_status_hint') }}</p>
                        </div>
                    @endif
                </x-ui.form-section>
            @endif

            @if ($section === 'access')
                <x-ui.form-section
                    :title="__('platform::app.user_role')"
                    :description="__('platform::app.user_role_hint_new')"
                >
                    <x-ts-select.styled
                        label="{{ __('platform::app.user_role') }}"
                        wire:model.live="form.role"
                        :options="$roles"
                        option-label="label"
                        option-value="value"
                        placeholder="{{ __('platform::app.user_role_ph') }}"
                        :hint="$record ? __('platform::app.user_role_hint_edit') : __('platform::app.user_role_hint_new')"
                    />
                </x-ui.form-section>

                <x-ui.form-section
                    stacked
                    :title="__('platform::app.user_permissions')"
                    :description="__('platform::app.user_permissions_desc')"
                >
                    @if ($record && $permissionList !== $originalPermissions)
                        <div class="mb-3 flex justify-end">
                            <x-ts-button
                                color="secondary"
                                sm
                                icon="arrow-path"
                                wire:click="restorePermissions"
                            >
                                {{ __('platform::app.user_restore_perms') }}
                            </x-ts-button>
                        </div>
                    @endif

                    <div class="space-y-6">
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
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
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
                                                    <span class="text-xs font-semibold leading-tight text-content-muted">{{ $permLabel }}</span>

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
                </x-ui.form-section>
            @endif

            <x-slot:footer>
                <x-ui.form-footer
                    :cancel-route="route('access.users.index')"
                    :label="$record ? __('platform::app.user_btn_update') : __('platform::app.user_btn_create')"
                >
                    {{ $record?->email }}
                </x-ui.form-footer>
            </x-slot:footer>
        </x-ui.form-shell>
        </div>
    </div>
</div>
