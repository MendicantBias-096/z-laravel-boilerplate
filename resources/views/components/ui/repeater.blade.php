@props([
    'field',
    'label' => null,
    'type' => 'text',
    'options' => [],
    'addLabel' => null,
    'min' => 1,
    'sortable' => false,
])

{{--
    Lista de un campo que se repite. El array vive en el componente, así que
    aquí no hay estado: solo `wire:model` contra `campo.N` y los tres métodos
    de HasRepeatableFields.

    `type` acepta lo que acepta `x-ts-input` —text, number, email, tel— o
    `select`, que pinta un desplegable con `:options`.
--}}

@php
    $rows = data_get($this, $field, []);
    $addLabel ??= __('platform::repeater.add');
@endphp

<div class="flex flex-col gap-2" wire:key="repeater-{{ $field }}">
    @foreach ($rows as $i => $row)
        {{--
            La clave lleva el índice porque al reordenar los valores se mueven
            con él: sin `wire:key`, Livewire reutiliza el nodo equivocado y el
            texto de una fila aparece en otra.
        --}}
        <div class="flex items-start gap-2" wire:key="repeater-{{ $field }}-{{ $i }}">
            <div class="grow">
                @if ($type === 'select')
                    <x-ts-select.native
                        :label="$i === 0 ? $label : null"
                        :options="$options"
                        wire:model="{{ $field }}.{{ $i }}"
                    />
                @else
                    <x-ts-input
                        :label="$i === 0 ? $label : null"
                        :type="$type"
                        wire:model="{{ $field }}.{{ $i }}"
                    />
                @endif
            </div>

            <div @class(['flex gap-1', 'pt-6' => $i === 0 && $label])>
                @if ($sortable)
                    <x-ts-button.circle
                        icon="chevron-up"
                        color="secondary"
                        sm
                        :disabled="$i === 0"
                        wire:click="moveRepeatable('{{ $field }}', {{ $i }}, -1)"
                        :title="__('platform::repeater.move_up')"
                    />
                    <x-ts-button.circle
                        icon="chevron-down"
                        color="secondary"
                        sm
                        :disabled="$i === count($rows) - 1"
                        wire:click="moveRepeatable('{{ $field }}', {{ $i }}, 1)"
                        :title="__('platform::repeater.move_down')"
                    />
                @endif

                {{-- Por debajo del mínimo no se quita: un formulario sin
                     ninguna fila deja al usuario sin sitio donde escribir y
                     sin nada que pulsar salvo «Agregar». --}}
                <x-ts-button.circle
                    icon="trash"
                    color="red"
                    sm
                    :disabled="count($rows) <= $min"
                    wire:click="removeRepeatable('{{ $field }}', {{ $i }})"
                    :title="__('platform::repeater.remove')"
                />
            </div>
        </div>

        @error("{$field}.{$i}")
            <span class="text-sm text-red-500">{{ $message }}</span>
        @enderror
    @endforeach

    <div>
        <x-ts-button
            icon="plus"
            color="secondary"
            sm
            wire:click="addRepeatable('{{ $field }}')"
        >
            {{ $addLabel }}
        </x-ts-button>
    </div>
</div>
