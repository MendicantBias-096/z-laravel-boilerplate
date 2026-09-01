@props([
    'field',
    'rowView',
    'label' => null,
    'addLabel' => null,
    'min' => 1,
    'sortable' => false,
])

{{--
    Repite un bloque de campos. Los campos los define el consumidor en
    `row-view`, que recibe `$field`, `$key` y `$row`:

        <x-ui.block-repeater field="experimentos" row-view="billing::experimentos._fila" />

    y en `_fila.blade.php`, con `wire:model` apuntando a la clave del bloque:

        x-ts-input   wire:model="FIELD.BLOCKKEY.nombre"
        input[file]  wire:model="FIELD.BLOCKKEY.comprobante"

    (escrito sin llaves a propósito: un `--` seguido de dos llaves dentro de
    este comentario lo cierra antes de tiempo, y el ejemplo pasa a compilarse
    como código de verdad.)

    Se llama `$blockKey` y no `$key` porque el compilador de Livewire 4 trata
    `key` como suya —`SupportCompiledWireKeys`— y la variable desaparece dentro
    del parcial incluido.

    Es un uuid y no una posición: ver el porqué en HasRepeatableBlocks.
    Interpolarlo en `wire:model` es lo que mantiene cada archivo en su bloque
    cuando se elimina o se reordena.
--}}

@php
    $rows = $this->{$field} ?? [];
    $addLabel ??= __('platform::repeater.add');
    $keys = array_keys($rows);
@endphp

<div class="flex flex-col gap-4" wire:key="blocks-{{ $field }}">
    @foreach ($rows as $key => $row)
        <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700" wire:key="block-{{ $field }}-{{ $key }}">
            <div class="mb-3 flex items-center justify-between">
                <span class="text-sm font-medium">
                    {{ $label }} {{ $loop->iteration }}
                </span>

                <div class="flex gap-1">
                    @if ($sortable)
                        <x-ts-button.circle
                            icon="chevron-up"
                            color="secondary"
                            sm
                            :disabled="$loop->first"
                            wire:click="moveBlock('{{ $field }}', '{{ $key }}', -1)"
                            :title="__('platform::repeater.move_up')"
                        />
                        <x-ts-button.circle
                            icon="chevron-down"
                            color="secondary"
                            sm
                            :disabled="$loop->last"
                            wire:click="moveBlock('{{ $field }}', '{{ $key }}', 1)"
                            :title="__('platform::repeater.move_down')"
                        />
                    @endif

                    <x-ts-button.circle
                        icon="trash"
                        color="red"
                        sm
                        :disabled="count($rows) <= $min"
                        wire:click="removeBlock('{{ $field }}', '{{ $key }}')"
                        :title="__('platform::repeater.remove')"
                    />
                </div>
            </div>

            @include($rowView, ['field' => $field, 'blockKey' => $key, 'row' => $row])
        </div>
    @endforeach

    <div>
        <x-ts-button
            icon="plus"
            color="secondary"
            sm
            wire:click="addBlock('{{ $field }}')"
        >
            {{ $addLabel }}
        </x-ts-button>
    </div>
</div>
