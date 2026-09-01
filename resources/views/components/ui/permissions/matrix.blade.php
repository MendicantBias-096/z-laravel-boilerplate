@props([
    'matrix' => ['tables' => [], 'blocks' => []],
    // Se muestra pero no se edita. Un control vivo que no guarda nada promete
    // algo que no se va a cumplir.
    'disabled' => false,
])

{{--
    La matriz de permisos, compartida por Usuarios y por Roles.

    Reparte los permisos sobre la misma rejilla —grupo → módulo → verbo— con
    maestras de fila y de columna. Las dos pantallas reparten exactamente lo
    mismo; lo único que cambia es a quién se le asigna al guardar, y de eso se
    encarga cada componente.

    Espera del componente que la use el trait `WithPermissionMatrix`: de ahí
    salen `moduleOptions`, `columnState`, `moduleState` y
    `permissionAnnouncement`.

    Ranura `acciones`: lo que va junto al filtro y no es común a las dos.
--}}

@php
    $etiquetaDe = fn (string $p): string => __('access::roles.permissions')[$p] ?? $p;
    $descripcionDe = fn (string $p): string => __('access::roles.descriptions')[$p] ?? '';
@endphp

<div>
    {{-- Un catálogo en vez de una caja de texto: lo que se busca es «enséñame
         este módulo entero», y escribir a ciegas deja fuera los grupos. --}}
    <div class="mb-6 flex flex-wrap items-end gap-3">
        <div class="min-w-0 flex-1 sm:max-w-sm">
            <x-ts-select.styled
                wire:model.live="moduleFilter"
                :label="__('platform::app.user_permissions_filter')"
                :options="$this->moduleOptions"
                option-label="label"
                option-value="value"
                :placeholder="__('platform::app.user_permissions_filter_ph')"
                multiple
                searchable
            />
        </div>

        {{ $acciones ?? '' }}
    </div>

    {{-- Alternar un módulo o una columna voltea varias casillas de una vez y el
         lector solo anuncia aquella donde está el foco. El nodo existe siempre:
         una región viva insertada junto con su texto no se anuncia. --}}
    <p role="status" wire:key="perm-live" class="sr-only">{{ $this->permissionAnnouncement }}</p>

    @if ($matrix['tables'] === [] && $matrix['blocks'] === [])
        <p class="py-12 text-center text-sm text-content-muted">
            {{ __('platform::app.user_permissions_no_results') }}
        </p>
    @endif

    <div class="space-y-10">
        {{-- Lo que comparte eje: una tabla por grupo. --}}
        @foreach ($matrix['tables'] as $group => $table)
            <section aria-labelledby="grupo-{{ $group }}" wire:key="tabla-{{ $group }}">
                <h3 id="grupo-{{ $group }}" class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.14em] text-content">
                    @svg(config("roles.group_icons.{$group}", 'lucide-folder'), 'size-4 text-content-muted', ['aria-hidden' => 'true'])
                    {{ __("access::roles.groups.{$group}") }}
                </h3>

                {{-- El contenedor con desplazamiento necesita foco propio, o con
                     teclado no hay forma de llegar a las columnas que quedan
                     fuera de la vista. --}}
                <div role="region" aria-labelledby="grupo-{{ $group }}" tabindex="0" class="overflow-x-auto rounded-lg border border-line">
                    <table class="w-full border-collapse text-sm">
                        <caption class="sr-only">
                            {{ __('platform::app.user_perm_table_caption', ['group' => __("access::roles.groups.{$group}")]) }}
                        </caption>

                        <thead>
                            <tr class="border-b border-line bg-panel-alt">
                                {{-- La sangría iguala la casilla más su hueco, para
                                     que el rótulo caiga sobre el nombre del módulo
                                     y no sobre el cuadro. --}}
                                <th scope="col" class="sticky start-0 z-20 w-64 bg-panel-alt py-2.5 pe-4 ps-[2.625rem] text-start text-[0.6875rem] font-semibold uppercase tracking-[0.14em] text-content">
                                    {{ __('platform::app.user_perm_module_col') }}
                                </th>

                                @foreach ($table['columns'] as $verb)
                                    @php $columnState = $this->columnState($group, $verb); @endphp
                                    <th scope="col" class="w-24 border-s border-line p-0">
                                        <button
                                            type="button"
                                            wire:click="toggleColumn('{{ $group }}', '{{ $verb }}')"
                                            role="checkbox"
                                            aria-checked="{{ ['all' => 'true', 'some' => 'mixed', 'none' => 'false'][$columnState] }}"
                                            aria-label="{{ __('platform::app.user_perm_toggle_col', ['verb' => __("access::roles.verbs.{$verb}")]) }}"
                                            @disabled($disabled)
                                            @class([
                                                'flex w-full flex-col items-center gap-1.5 px-2 py-2 text-[0.6875rem] font-semibold uppercase tracking-[0.08em] text-content-muted',
                                                'cursor-pointer transition-colors hover:bg-panel hover:text-content' => ! $disabled,
                                            ])
                                        >
                                            {{ __("access::roles.verbs.{$verb}") }}
                                            <x-ui.permissions.box :state="$columnState" />
                                        </button>
                                    </th>
                                @endforeach

                                <th scope="col" class="w-56 border-s border-line px-3 py-2.5 text-start text-[0.6875rem] font-semibold uppercase tracking-[0.08em] text-content-subtle">
                                    {{ __('platform::app.user_perm_extras') }}
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($table['rows'] as $module => $row)
                                @php
                                    $moduleState = $this->moduleState($module);
                                    $propios = config("roles.permissions.{$module}", []);
                                @endphp
                                <tr wire:key="fila-{{ $module }}" class="border-b border-line bg-panel last:border-b-0">
                                    {{-- Fijada al inicio: la tabla se desplaza, y
                                         una celda marcada sin su módulo a la vista
                                         no dice nada.

                                         `h-px` en la celda más `h-full` en el botón
                                         es lo que hace que el botón herede el alto
                                         real de la fila; sin eso, una fila más alta
                                         por sus etiquetas deja el hover flotando. --}}
                                    <th scope="row" class="sticky start-0 z-10 h-px bg-panel p-0 text-start font-normal">
                                        <button
                                            type="button"
                                            wire:click="toggleModule('{{ $module }}')"
                                            role="checkbox"
                                            aria-checked="{{ ['all' => 'true', 'some' => 'mixed', 'none' => 'false'][$moduleState] }}"
                                            aria-label="{{ __('platform::app.user_perm_toggle_all', ['module' => __("access::roles.modules.{$module}")]) }}"
                                            @disabled($disabled)
                                            @class([
                                                'flex h-full min-h-11 w-full min-w-44 items-center gap-2.5 px-4',
                                                'cursor-pointer transition-colors hover:bg-panel-alt' => ! $disabled,
                                            ])
                                        >
                                            <x-ui.permissions.box :state="$moduleState" />
                                            <span class="truncate text-sm font-medium text-content">
                                                {{ __("access::roles.modules.{$module}") }}
                                            </span>
                                            <span class="ms-auto shrink-0 ps-3 text-xs font-medium tabular-nums text-content-subtle" aria-hidden="true">
                                                {{ count(array_intersect($propios, $this->permissionList)) }}/{{ count($propios) }}
                                            </span>
                                        </button>
                                    </th>

                                    @foreach ($table['columns'] as $verb)
                                        @php $permission = $row['cells'][$verb]; @endphp

                                        @if ($permission === null)
                                            {{-- Una raya dice «este permiso no
                                                 existe». Una casilla apagada diría
                                                 «existe y no lo marcaste». --}}
                                            <td class="border-s border-line text-center align-middle">
                                                <span class="select-none text-content-subtle/60" aria-hidden="true">&mdash;</span>
                                                <span class="sr-only">{{ __('platform::app.user_perm_absent') }}</span>
                                            </td>
                                        @else
                                            @php
                                                $marcado = in_array($permission, $this->permissionList, true);
                                                $id = 'celda-'.\Illuminate\Support\Str::slug($permission);
                                            @endphp
                                            <td @class(['border-s border-line p-0', 'bg-primary-500/[0.08]' => $marcado])>
                                                <label @class([
                                                    'flex h-11 items-center justify-center',
                                                    'cursor-pointer transition-colors hover:bg-panel-alt' => ! $disabled,
                                                ])>
                                                    <span class="relative size-4">
                                                        <input
                                                            type="checkbox"
                                                            wire:model.live="permissionList"
                                                            wire:key="{{ $id }}"
                                                            value="{{ $permission }}"
                                                            aria-label="{{ __('platform::app.user_perm_cell', ['permission' => $etiquetaDe($permission), 'module' => __("access::roles.modules.{$module}")]) }}"
                                                            aria-describedby="{{ $id }}-desc"
                                                            @disabled($disabled)
                                                            class="peer absolute inset-0 cursor-pointer opacity-0"
                                                        />
                                                        <span class="pointer-events-none absolute inset-0 rounded border border-content-subtle bg-panel transition-colors peer-checked:border-primary-600 peer-checked:bg-primary-600 peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-primary-600 dark:peer-checked:border-primary-500 dark:peer-checked:bg-primary-500"></span>
                                                        <span class="pointer-events-none absolute inset-0 flex items-center justify-center opacity-0 transition-opacity peer-checked:opacity-100">
                                                            @svg('lucide-check', 'size-2.5 text-white', ['aria-hidden' => 'true'])
                                                        </span>
                                                    </span>
                                                    <span id="{{ $id }}-desc" class="sr-only">{{ $descripcionDe($permission) }}</span>
                                                </label>
                                            </td>
                                        @endif
                                    @endforeach

                                    {{-- Lo que este módulo tiene y los demás no,
                                         con su etiqueta puesta porque no comparte
                                         columna con nadie. --}}
                                    <td class="border-s border-line px-3">
                                        <div class="flex flex-wrap gap-1.5 py-2">
                                            @foreach ($row['extras'] as $extra)
                                                <x-ui.permissions.chip
                                                    :permission="$extra"
                                                    :module="$module"
                                                    :granted="in_array($extra, $this->permissionList, true)"
                                                    :disabled="$disabled"
                                                />
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endforeach

        {{-- Lo que no comparte eje con nadie: listas de etiquetas. --}}
        @foreach ($matrix['blocks'] as $group => $modules)
            <section aria-labelledby="grupo-{{ $group }}" wire:key="bloque-{{ $group }}">
                <h3 id="grupo-{{ $group }}" class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.14em] text-content">
                    @svg(config("roles.group_icons.{$group}", 'lucide-folder'), 'size-4 text-content-muted', ['aria-hidden' => 'true'])
                    {{ __("access::roles.groups.{$group}") }}
                </h3>

                <div class="rounded-lg border border-line bg-panel">
                    @foreach ($modules as $module => $permissions)
                        <div wire:key="bloque-mod-{{ $module }}" class="border-b border-line p-4 last:border-b-0">
                            <p class="mb-2.5 text-sm font-medium text-content">
                                {{ __("access::roles.modules.{$module}") }}
                            </p>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($permissions as $permission)
                                    <x-ui.permissions.chip
                                        :permission="$permission"
                                        :module="$module"
                                        :granted="in_array($permission, $this->permissionList, true)"
                                        :disabled="$disabled"
                                    />
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>
</div>
