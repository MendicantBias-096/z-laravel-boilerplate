@props([
    'title' => '',
    'description' => '',
    'icon' => 'lucide-file-text',
    // Con `action` en `null` el chasis no envuelve en `<form>`: es el caso de
    // una pantalla donde cada bloque guarda lo suyo por su cuenta, como unos
    // ajustes, y un submit único no significaría nada.
    //
    // De ahí sale también dónde vive el scroll. El cuerpo se desplaza por
    // dentro para que el pie con «Guardar» no quede fuera de alcance; sin pie
    // no hay nada que mantener a la vista, así que el contenido fluye y se
    // desplaza la página, que es lo que se espera de una pantalla de ajustes.
    //
    // El valor por defecto es `null` y no una acción: en Blade, pasar
    // `:action="null"` cae al default del `@props` —un null explícito se trata
    // como «no proporcionado»—, así que con el default al revés una pantalla
    // sin formulario nunca conseguiría apagarlo.
    'action' => null,
])

{{--
    El cuerpo de la sección abierta: la columna derecha de la caja.

    Tres franjas: encabezado y pie anclados, y en medio lo único que se
    desplaza. La caja mide siempre lo mismo, así que cambiar de sección ya no
    reacomoda la página —una sección puede tener dos campos y otra ochenta— y
    los botones de guardar nunca quedan fuera de la vista.

    El encabezado comparte alto con el del menú (h-12): juntos forman una sola
    franja que cruza la caja, partida por el mismo borde que separa las dos
    columnas.

    Cada eslabón lleva `min-h-0` a propósito: sin eso un hijo flexible crece
    con su contenido en lugar de ceñirse al padre, y el desplazamiento del
    cuerpo no llega a activarse nunca.
--}}
<{{ $action ? 'form' : 'div' }}
    @if ($action) wire:submit="{{ $action }}" @endif
    @class([
        'flex flex-col bg-panel',
        'min-h-0 flex-1' => $action,
    ])
>
    <header @class([
        'flex h-12 shrink-0 items-center gap-2.5 border-b border-line bg-panel px-4 sm:px-5',
        // Con scroll de página el encabezado se queda arriba del todo si no se
        // pega: al bajar se pierde de vista en qué sección se está.
        'sticky top-0 z-10' => ! $action,
    ])>
        <span class="flex size-7 shrink-0 items-center justify-center rounded-lg bg-primary-50 dark:bg-primary-950">
            @svg($icon, 'size-4 text-primary-600 dark:text-primary-400', ['aria-hidden' => 'true'])
        </span>

        <h2 class="shrink-0 text-sm font-semibold text-content">{{ $title }}</h2>

        @if ($description)
            <span class="hidden text-content-subtle sm:inline" aria-hidden="true">&middot;</span>
            <p class="hidden min-w-0 truncate text-xs text-content-muted sm:block">{{ $description }}</p>
        @endif
    </header>

    <div @class([
        'px-5 py-6 sm:px-8 sm:py-7',
        'min-h-0 flex-1 overflow-y-auto' => $action,
    ])>
        {{ $slot }}
    </div>

    {{ $footer ?? '' }}
</{{ $action ? 'form' : 'div' }}>
