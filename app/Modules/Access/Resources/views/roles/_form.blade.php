<div class="flex min-h-0 flex-1 flex-col">
    {{--
        El rol, en la misma caja que la ficha de usuario.

        Aquí no hay menú de secciones: un rol es una sola cosa —un nombre y una
        plantilla de permisos—, y un menú de una entrada es un menú de mentira.
        Lo que sí se conserva es el chasis: encabezado y pie anclados con el
        cuerpo desplazándose en medio, para que «Guardar» no quede fuera de
        alcance cuando la matriz crece.
    --}}
    <div class="form-card flex min-h-0 flex-1 flex-col rounded-lg border border-line">

        <x-ui.form-shell
            action="save"
            icon="lucide-shield"
            :title="$record ? $record->display_name : __('platform::app.role_btn_create')"
            :description="__('platform::app.role_permissions_desc')"
        >
            <x-slot name="footer">
                <x-ui.form-footer
                    :cancel-route="route('access.roles.index')"
                    :label="__('platform::app.role_btn_save')"
                >
                    {{ trans_choice('platform::app.role_permissions_count', count($permissionList), ['count' => count($permissionList)]) }}
                </x-ui.form-footer>
            </x-slot>

            @if ($this->isProtected)
                {{-- Un rol de plataforma se muestra pero no se edita: el seed
                     lo devolvería a su sitio en el siguiente despliegue, así
                     que dejar los campos vivos promete algo que no se va a
                     cumplir. --}}
                <div class="mb-6 flex items-start gap-3 rounded-lg border border-amber-400/40 bg-amber-50 px-4 py-3 dark:border-amber-600/40 dark:bg-amber-950/30">
                    @svg('lucide-lock', 'mt-0.5 size-4 shrink-0 text-amber-600 dark:text-amber-400', ['aria-hidden' => 'true'])
                    <div>
                        <p class="text-sm font-semibold text-amber-900 dark:text-amber-200">{{ __('platform::app.role_protected') }}</p>
                        <p class="text-xs text-amber-800 dark:text-amber-300/80">{{ __('platform::app.role_protected_desc') }}</p>
                    </div>
                </div>
            @endif

            <x-ui.form-section
                :title="__('platform::app.role_name')"
                :description="__('platform::app.role_name_hint')"
            >
                <div class="max-w-sm space-y-2">
                    <x-ts-input
                        wire:model.live.blur="display_name"
                        placeholder="{{ __('platform::app.role_name_ph') }}"
                        :disabled="$this->isProtected"
                    />

                    {{-- El identificador se deriva del nombre y no se escribe:
                         mostrarlo evita la sorpresa de descubrir después que
                         «Administrador de cuentas» se llama en realidad
                         `administrador-de-cuentas`. --}}
                    @if ($name)
                        <p class="flex items-center gap-2 text-xs text-content-subtle">
                            {{ __('platform::app.role_identifier') }}
                            <code class="rounded bg-panel-alt px-2 py-0.5 font-mono text-content-muted">{{ $name }}</code>
                        </p>
                    @endif
                </div>
            </x-ui.form-section>

            {{-- La misma matriz que en la ficha del usuario. Lo que cambia no es
                 cómo se reparten los permisos sino a quién: aquí quedan en el
                 rol, que después sirve de plantilla al asignarlo. --}}
            <x-ui.form-section
                stacked
                :title="__('platform::app.role_permissions')"
                :description="__('platform::app.role_permissions_matrix_desc')"
            >
                <x-ui.permissions.matrix
                    :matrix="$this->permissionMatrix"
                    :disabled="$this->isProtected"
                />
            </x-ui.form-section>
        </x-ui.form-shell>
    </div>
</div>
