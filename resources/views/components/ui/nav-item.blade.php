@props([
    'label',
    'icon'        => null,
    'route',
    'activeRoute' => null,
    'indent'      => false,
])

@php
    $url      = route($route);
    $basePath = parse_url($url, PHP_URL_PATH);
    $isActive = request()->routeIs($activeRoute ?? $route);
@endphp

<a
    href="{{ $url }}"
    wire:navigate
    x-data="{
        isActive: {{ $isActive ? 'true' : 'false' }},
        base: @js($basePath),
        check() {
            this.isActive = window.location.pathname === this.base
                || window.location.pathname.startsWith(this.base + '/');
        },
        init() {
            document.addEventListener('livewire:navigated', () => this.check());
        },
    }"
    class="group flex items-center gap-2 py-0.5 text-sm font-medium transition {{ $indent ? 'px-5 ps-12' : 'px-5' }}"
    :class="isActive
        ? 'border-r-2 border-primary-500 bg-primary-50 text-primary-600 hover:bg-primary-100 dark:bg-primary-950/50 dark:text-primary-400 dark:border-primary-400 dark:hover:bg-primary-950/70'
        : 'text-content-muted hover:bg-panel-alt hover:text-content'"
>
    @if ($icon)
        @svg($icon, 'size-5 flex-none opacity-80 transition-colors')
    @endif

    <span class="grow py-2">{{ __($label) }}</span>
</a>
