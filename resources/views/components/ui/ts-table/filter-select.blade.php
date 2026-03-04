@props(['label', 'placeholder' => 'Todos', 'options' => []])

@php
    // Extrae el nombre de la propiedad Livewire desde wire:model o wire:model.live
    $propName = $attributes->whereStartsWith('wire:model')->first() ?? '';
@endphp

<div
    class="flex w-64 flex-col gap-2"
    x-data="{
        open: false,
        search: '',
        value: $wire.entangle('{{ $propName }}').live,
        pos: { top: 0, left: 0, width: 0 },
        options: @js($options),

        toggle(el) {
            const r = el.closest('[data-filter-select]').getBoundingClientRect();
            this.pos = { top: r.bottom + 4, left: r.left, width: r.width };
            this.open = !this.open;
            if (this.open) this.$nextTick(() => this.$refs.search?.focus());
        },
        close() { this.open = false; this.search = ''; },

        get filtered() {
            const q = this.search.toLowerCase();
            return Object.entries(this.options).filter(([, t]) => t.toLowerCase().includes(q));
        },
        selectedLabel() {
            if (!this.value) return '{{ $placeholder }}';
            const t = this.options[this.value];
            return t ? t.charAt(0).toUpperCase() + t.slice(1) : '{{ $placeholder }}';
        },
        select(val) {
            this.value = val;
            this.close();
        },
    }"
    @click.window="if (!$el.contains($event.target) && !($refs.panel && $refs.panel.contains($event.target))) close()"
    @keydown.escape.window="close()"
    data-filter-select
>
    <label class="text-xs font-medium text-content-muted">{{ $label }}</label>

    {{-- Trigger --}}
    <button
        type="button"
        @click="toggle($el)"
        class="flex w-full cursor-pointer items-center justify-between rounded-lg border border-line bg-panel px-3 py-2 text-sm text-content transition-colors hover:border-primary-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
        :class="value ? 'border-primary-400' : ''"
    >
        <span :class="value ? 'text-content' : 'text-content-subtle'" x-text="selectedLabel()"></span>
        <x-ui.icon name="chevron-down"
            class="size-3.5 shrink-0 text-content-subtle transition-transform duration-150"
            ::class="{ 'rotate-180': open }" />
    </button>

    {{-- Panel teletransportado --}}
    <template x-teleport="#app-root">
        <div
            x-ref="panel"
            x-show="open"
            x-cloak
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 -translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-1"
            :style="`position:fixed; top:${pos.top}px; left:${pos.left}px; width:${pos.width}px; z-index:9999;`"
            class="overflow-hidden rounded-lg border border-line bg-white shadow-lg dark:border-dark-600 dark:bg-dark-700"
        >
            {{-- Búsqueda --}}
            <div class="border-b border-line p-2">
                <div class="relative">
                    <x-ui.icon name="search"
                        class="pointer-events-none absolute left-2.5 top-1/2 size-3.5 -translate-y-1/2 text-content-subtle" />
                    <input
                        x-ref="search"
                        x-model="search"
                        type="text"
                        placeholder="Buscar..."
                        class="w-full rounded-md border border-line bg-panel-alt py-1.5 pl-8 pr-3 text-sm text-content placeholder-content-subtle focus:border-primary-500 focus:outline-none"
                    />
                </div>
            </div>

            {{-- Opciones --}}
            <div class="max-h-48 overflow-y-auto py-1">
                {{-- Opción vacía --}}
                <button
                    type="button"
                    @click="select('')"
                    class="flex w-full cursor-pointer items-center gap-2 px-3 py-2 text-sm transition-colors hover:bg-gray-50 dark:hover:bg-dark-600"
                    :class="!value ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-content-muted'"
                >
                    {{ $placeholder }}
                </button>

                <template x-for="[val, text] in filtered" :key="val">
                    <button
                        type="button"
                        @click="select(val)"
                        class="flex w-full cursor-pointer items-center justify-between gap-2 px-3 py-2 text-sm transition-colors hover:bg-gray-50 dark:hover:bg-dark-600"
                        :class="value === val ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-content'"
                    >
                        <span x-text="text.charAt(0).toUpperCase() + text.slice(1)"></span>
                        <x-ui.icon name="check" class="size-3.5 shrink-0"
                            x-show="value === val" />
                    </button>
                </template>

                <div x-show="filtered.length === 0"
                     class="px-3 py-4 text-center text-sm text-content-subtle">
                    Sin resultados
                </div>
            </div>
        </div>
    </template>
</div>
