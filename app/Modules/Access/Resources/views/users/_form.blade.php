<div class="flex min-h-0 flex-1 flex-col">
    {{--
        El chasis de formulario del proyecto: menú de secciones a la izquierda,
        cuerpo con scroll propio a la derecha y pie anclado con los botones.

        La caja mide siempre lo mismo, así que cambiar de sección no reacomoda
        la página —Identidad tiene cuatro campos y Accesos ochenta— y «Guardar»
        no se va nunca fuera de la vista.
    --}}
    <div class="form-card flex min-h-0 flex-1 flex-col overflow-hidden rounded-lg border border-line lg:flex-row">

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

        <div wire:key="section-{{ $section }}" class="section-panel flex min-h-0 min-w-0 flex-1 flex-col">
        <x-ui.form-shell
            action="save"
            :title="$record ? $record->username : __('platform::app.user_btn_create')"
            :description="__('platform::app.user_form_hint')"
            :icon="$this->sections[array_search($section, array_column($this->sections, 'key'), true) ?: 0]['icon']"
        >
            <x-slot name="footer">
                <x-ui.form-footer
                    :cancel-route="route('access.users.index')"
                    :label="$record ? __('platform::app.user_btn_update') : __('platform::app.user_btn_create')"
                >
                    {{ $record?->email }}
                </x-ui.form-footer>
            </x-slot>

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
                    <x-ui.permissions.matrix :matrix="$this->permissionMatrix">
                        <x-slot name="acciones">
                            @if ($record && $permissionList !== $originalPermissions)
                                <button
                                    type="button"
                                    wire:click="restorePermissions"
                                    class="inline-flex shrink-0 cursor-pointer items-center gap-1.5 pb-2 text-sm font-medium text-content-muted transition-colors hover:text-content"
                                >
                                    @svg('lucide-rotate-ccw', 'size-3.5', ['aria-hidden' => 'true'])
                                    {{ __('platform::app.user_restore_perms') }}
                                </button>
                            @endif
                        </x-slot>
                    </x-ui.permissions.matrix>
                </x-ui.form-section>
            @endif

        </x-ui.form-shell>
        </div>
    </div>
</div>
