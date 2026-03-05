@props(['label', 'placeholder' => '', 'icon' => null])

<div class="flex w-64 flex-col gap-2">
    <label class="text-xs font-medium text-content-muted">{{ $label }}</label>
    <div class="relative">
        @if ($icon)
            <x-ui.icon :name="$icon"
                class="pointer-events-none absolute left-3 top-1/2 size-3.5 -translate-y-1/2 text-content-subtle" />
        @endif
        <input
            {{ $attributes->merge(['type' => 'text']) }}
            placeholder="{{ $placeholder }}"
            class="{{ $icon ? 'pl-10' : 'px-3' }} w-full cursor-text rounded-lg border border-line bg-panel py-2 pr-3 text-sm text-content placeholder-content-subtle focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:bg-panel"
        />
    </div>
</div>
