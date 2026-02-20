@props([
    'label',
    'icon'        => null,
    'route',
    'activeRoute' => null,
    'indent'      => false,
])

@php
    $isActive = request()->routeIs($activeRoute ?? $route);
@endphp

<a
    href="{{ route($route) }}"
    wire:navigate
    @class([
        'group flex items-center gap-2 py-0.5 text-sm font-medium transition',
        $indent ? 'px-5 ps-12' : 'px-5',
        'border-r-2 border-primary-500 bg-primary-50 text-primary-800 dark:bg-primary-950/50 dark:text-primary-200 dark:border-primary-400' => $isActive,
        'text-content-muted hover:bg-panel-alt hover:text-content' => ! $isActive,
    ])
>
    @if ($icon)
        <span class="flex flex-none items-center opacity-75">
            <x-ui.icon
                :name="$icon"
                @class([
                    'size-5',
                    'text-primary-600 dark:text-primary-400' => $isActive,
                    'text-content-subtle group-hover:text-content-muted' => ! $isActive,
                ])
            />
        </span>
    @endif

    <span class="grow py-2">{{ __($label) }}</span>
</a>
