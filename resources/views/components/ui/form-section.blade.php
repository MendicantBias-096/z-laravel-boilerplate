@props(['title' => '', 'description' => '', 'stacked' => false])

{{--
    Un apartado dentro de un formulario: el rótulo explica a la izquierda, los
    controles ocupan la derecha.

    Resuelve dos cosas a la vez: aprovecha el ancho sin estirar los campos, y
    le da a cada bloque un sitio donde decir qué es sin robarle espacio al
    formulario.

    Por debajo de `lg` se apila, porque en una columna estrecha un rótulo al
    lado deja los campos sin aire.

    La clase `form-divider` dibuja el filete que lo separa del apartado
    anterior; el primero no lleva.

    Con `stacked` el rótulo se pone encima y el contenido usa el ancho entero.
    Es para lo que no cabe en dos tercios: una tabla, una rejilla ancha.
    Reservar un tercio a un rótulo de dos líneas mientras la tabla se aprieta
    al lado es repartir mal el espacio.
--}}
<div @class([
    'form-divider pb-7 last:pb-0',
    'lg:grid lg:grid-cols-3 lg:gap-8' => ! $stacked,
])>
    <div @class(['lg:col-span-1' => ! $stacked])>
        <h3 class="text-sm font-semibold text-content">{{ $title }}</h3>
        @if ($description)
            <p class="mt-1 max-w-[45ch] text-xs leading-relaxed text-content-muted">
                {{ $description }}
            </p>
        @endif
    </div>

    <div @class(['mt-4', 'lg:col-span-2 lg:mt-0' => ! $stacked])>
        {{ $slot }}
    </div>
</div>
