@props([
    'cancelRoute' => null,
    'label' => null,
    'icon' => 'lucide-save',
    'target' => 'save',
])

@php
    $label ??= __('platform::form.save');
@endphp

{{--
    El pie anclado del chasis: a la izquierda lo que la pantalla quiera contar
    de su estado, a la derecha los botones. Vacío no ocupa, porque el
    `justify-between` deja los botones a la derecha igual.
--}}
<div class="flex h-14 shrink-0 items-center justify-between gap-3 border-t border-line px-4 sm:px-5">
    <p class="min-w-0 truncate text-xs text-content-muted">{{ $slot }}</p>

    <div class="flex shrink-0 items-center gap-2">
        @if ($cancelRoute)
            <x-ts-button color="secondary" sm :href="$cancelRoute" wire:navigate>
                {{ __('platform::form.cancel') }}
            </x-ts-button>
        @endif

        <x-ts-button type="submit" sm wire:loading.attr="disabled" wire:target="{{ $target }}">
            {{ $label }}
        </x-ts-button>
    </div>
</div>
