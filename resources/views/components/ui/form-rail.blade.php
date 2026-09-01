@props([
    // Cada entrada: key, icon, label y, opcionalmente, `badge` con lo que deba
    // aparecer a la derecha —el «17/84» de unos permisos, por ejemplo—.
    'sections',
    'section' => null,
    'dirty' => [],
    'title' => null,
    'action' => 'goTo',
])

@php
    $section ??= $sections[0]['key'] ?? null;
    $title ??= __('platform::form.sections');
@endphp

{{--
    Menú de secciones: la columna izquierda de la caja.

    Lleva `panel-alt` mientras el contenido lleva `panel`, así que la división
    la hace el propio cambio de superficie y el borde solo la remata. Funciona
    en los dos temas: en claro el menú queda más oscuro que el contenido, en
    oscuro más claro.

    El padding horizontal vive en los ítems, no aquí: así la banda de la
    sección abierta llega de borde a borde en vez de flotar dentro del menú.
    Tampoco hay padding vertical: la primera opción arranca pegada al canto y
    el pie se descuelga al fondo, de modo que la columna se llena en vez de
    dejar aire suelto arriba y abajo.
--}}
<nav
    aria-label="{{ $title }}"
    class="flex min-h-0 flex-col border-b border-line bg-panel-alt lg:border-b-0 lg:border-e"
>
    <div class="hidden h-12 shrink-0 items-center border-b border-line px-4 lg:flex">
        <span class="text-xs font-semibold uppercase tracking-[0.1em] text-content-muted">
            {{ $title }}
        </span>
    </div>

    <ul class="flex shrink-0 overflow-x-auto scrollbar-none lg:flex-col lg:overflow-visible">
        @foreach ($sections as $item)
            @php
                $isCurrent = $section === $item['key'];
                $isDirty = ($dirty[$item['key']] ?? false) === true;
            @endphp
            <li class="shrink-0 lg:shrink">
                <button
                    type="button"
                    wire:click="{{ $action }}('{{ $item['key'] }}')"
                    @if ($isCurrent) aria-current="true" @endif
                    @class([
                        'group flex w-full cursor-pointer items-center gap-2.5 border-b-2 px-4 py-2.5 text-left text-sm transition-colors duration-150 lg:border-b-0 lg:border-e-2',
                        'border-primary-600 font-semibold text-content dark:border-primary-500' => $isCurrent,
                        'border-transparent font-medium text-content-muted hover:bg-panel/50 hover:text-content' => ! $isCurrent,
                    ])
                >
                    @svg($item['icon'], 'size-4 shrink-0 '.($isCurrent ? 'text-primary-600 dark:text-primary-400' : 'text-content-muted'), ['aria-hidden' => 'true'])

                    <span class="whitespace-nowrap">{{ $item['label'] }}</span>

                    <span class="ms-auto flex items-center gap-2 ps-2">
                        @if (filled($item['badge'] ?? null))
                            <span class="text-xs font-medium tabular-nums text-content-muted">
                                {{ $item['badge'] }}
                            </span>
                        @endif

                        {{-- El punto de «sin guardar». Un formulario partido en
                             secciones esconde lo que falta por guardar en las
                             que no se ven; esto es lo único que lo cuenta. --}}
                        @if ($isDirty)
                            <span
                                class="size-1.5 shrink-0 rounded-full bg-warning"
                                role="img"
                                aria-label="{{ __('platform::form.unsaved') }}"
                            ></span>
                        @endif
                    </span>
                </button>
            </li>
        @endforeach
    </ul>

    {{-- Lo que se descuelga al fondo del menú: el estado del registro, su
         fecha de alta, lo que la pantalla quiera contar. Vacío no ocupa. --}}
    @if (isset($pie))
        <div class="mt-auto hidden px-4 py-4 text-xs lg:block">{{ $pie }}</div>
    @endif
</nav>
