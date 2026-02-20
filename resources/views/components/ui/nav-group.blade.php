@props([
    'label',
    'icon'        => null,
    'activeRoute' => null,
    'items'       => [],
])

@php
    $visibleItems = collect($items)->filter(function ($item) {
        if (! isset($item['permission'])) return true;
        return auth()->user()?->can($item['permission']) ?? false;
    })->values();

    if ($visibleItems->isEmpty()) return;

    $groupActive = $activeRoute
        ? request()->routeIs($activeRoute)
        : $visibleItems->contains(
            fn ($item) => request()->routeIs($item['active_route'] ?? $item['route'])
        );
@endphp

<div x-data="{ open: {{ $groupActive ? 'true' : 'false' }} }">

    <button
        type="button"
        @click="open = !open"
        @class([
            'group flex w-full items-center gap-2 px-5 py-0.5 text-sm font-medium transition',
            $groupActive ? 'text-primary-800 dark:text-primary-200' : 'text-content-muted hover:bg-panel-alt hover:text-content',
        ])
    >
        @if ($icon)
            <span class="flex flex-none items-center opacity-75">
                <x-ui.icon
                    :name="$icon"
                    @class([
                        'size-5',
                        'text-primary-600 dark:text-primary-400' => $groupActive,
                        'text-content-subtle group-hover:text-content-muted' => ! $groupActive,
                    ])
                />
            </span>
        @endif

        <span class="grow py-2 text-left">{{ __($label) }}</span>

        <x-ui.icon
            name="chevron-down"
            class="size-4 text-content-subtle transition-transform duration-200"
            x-bind:class="{ 'rotate-180': open }"
        />
    </button>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
    >
        @foreach ($visibleItems as $item)
            <x-ui.nav-item
                :label="$item['label']"
                :route="$item['route']"
                :activeRoute="$item['active_route'] ?? null"
                :indent="true"
            />
        @endforeach
    </div>

</div>
