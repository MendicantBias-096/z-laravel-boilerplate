<div class="flex min-h-0 flex-1 flex-col">
    {{-- El mismo chasis que la ficha de usuario: dos pantallas hermanas con
         estructuras distintas se leen como descuido. --}}
    <div class="form-card flex min-h-0 flex-1 flex-col rounded-lg border border-line lg:flex-row">

        <x-ui.form-rail
            :sections="$this->sections"
            :section="$section"
            :title="__('platform::app.role_nav_title')"
        >
            @if ($this->isProtected)
                <x-slot:pie>
                    <div class="flex items-start gap-2 text-amber-600 dark:text-amber-400">
                        @svg('lucide-lock', 'mt-0.5 size-3.5 shrink-0', ['aria-hidden' => 'true'])
                        <span>{{ __('platform::app.role_protected') }}</span>
                    </div>
                </x-slot:pie>
            @endif
        </x-ui.form-rail>

        <div wire:key="section-{{ $section }}" class="section-panel flex min-h-0 min-w-0 flex-1 flex-col">
            <x-ui.form-shell
                action="save"
                :title="$record ? $record->display_name : __('platform::app.role_btn_create')"
                :description="__('platform::app.role_permissions_desc')"
                :icon="$this->sections[array_search($section, array_column($this->sections, 'key'), true) ?: 0]['icon']"
            >
                <x-slot name="footer">
                    <x-ui.form-footer
                        :cancel-route="route('access.roles.index')"
                        :label="__('platform::app.role_btn_save')"
                    >
                        {{ $name }}
                    </x-ui.form-footer>
                </x-slot>

                @if ($this->isProtected)
                    {{-- Un rol de plataforma se muestra pero no se edita: el
                         seed lo devolvería a su sitio en el siguiente despliegue,
                         así que dejar los campos vivos promete algo que no se
                         va a cumplir. --}}
                    <div class="mb-6 flex items-start gap-3 rounded-lg border border-amber-400/40 bg-amber-50 px-4 py-3 dark:border-amber-600/40 dark:bg-amber-950/30">
                        @svg('lucide-lock', 'mt-0.5 size-4 shrink-0 text-amber-600 dark:text-amber-400', ['aria-hidden' => 'true'])
                        <div>
                            <p class="text-sm font-semibold text-amber-900 dark:text-amber-200">{{ __('platform::app.role_protected') }}</p>
                            <p class="text-xs text-amber-800 dark:text-amber-300/80">{{ __('platform::app.role_protected_desc') }}</p>
                        </div>
                    </div>
                @endif

                @if ($section === 'identity')
                    <x-ui.form-section
                        :title="__('platform::app.role_section_identity')"
                        :description="__('platform::app.role_name_hint')"
                    >
                        <x-ts-input
                            label="{{ __('platform::app.role_name') }}"
                            wire:model.live="display_name"
                            placeholder="{{ __('platform::app.role_name_ph') }}"
                            :disabled="$this->isProtected"
                        />

                        @if ($name)
                            <div class="mt-2 flex items-center gap-2 text-xs text-content-subtle">
                                <span>{{ __('platform::app.role_identifier') }}:</span>
                                <code class="rounded bg-panel-alt px-2 py-0.5 font-mono text-content-muted">{{ $name }}</code>
                            </div>
                        @endif
                    </x-ui.form-section>
                @endif

                @if ($section === 'permissions')
                    <x-ui.form-section
                        stacked
                        :title="__('platform::app.role_permissions')"
                        :description="__('platform::app.role_permissions_desc')"
                    >
                        <x-ui.permissions.matrix
                            :matrix="$this->permissionMatrix"
                            :disabled="$this->isProtected"
                        />
                    </x-ui.form-section>
                @endif
            </x-ui.form-shell>
        </div>
    </div>
</div>
