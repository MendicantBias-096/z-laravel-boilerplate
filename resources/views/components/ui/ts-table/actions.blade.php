@props([
    'row',
    'editRoute'         => null,
    'editPermission'    => null,
    'deletePermission'  => null,
    'restorePermission' => null,
    'model'             => 'registro',
    'softDeletes'       => true,
])
@php $isTrashed = $softDeletes && method_exists($row, 'trashed') && $row->trashed(); @endphp

<div
    x-data="{
        open: false,
        pos: { top: 0, left: 0 },
        toggle(btn) {
            const r = btn.getBoundingClientRect();
            this.pos = { top: r.bottom + 4, left: r.right };
            this.open = !this.open;
        },
        close() { this.open = false; },
    }"
    @click.outside="close()"
    @keydown.escape.window="close()"
>
    <button
        type="button"
        @click="toggle($el)"
        class="cursor-pointer rounded-full p-1.5 text-gray-500 hover:bg-gray-200 dark:text-dark-300 dark:hover:bg-dark-500"
    >
        <x-ui.icon name="ellipsis-vertical" class="size-4" />
    </button>

    <template x-teleport="#app-root">
        <div
            x-show="open"
            x-cloak
            x-transition:enter="transition duration-100 ease-out"
            x-transition:enter-start="opacity-0 -translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-1"
            :style="`position:fixed; top:${pos.top}px; left:${pos.left}px; transform:translateX(-100%); z-index:9999;`"
            class="min-w-[9rem] overflow-hidden rounded-lg border border-gray-200 bg-white shadow-lg dark:border-dark-600 dark:bg-dark-700"
        >
            {{-- Editar (solo si no está eliminado) --}}
            @if (! $isTrashed && $editRoute && $editPermission)
                @can($editPermission)
                    <a
                        href="{{ route($editRoute, $row) }}"
                        wire:navigate
                        @click="close()"
                        class="flex w-full items-center gap-x-2 px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 dark:text-dark-300 dark:hover:bg-dark-600"
                    >
                        <x-ui.icon name="pencil" class="size-4 shrink-0 text-gray-400" />
                        {{ __('table.actions.edit') }}
                    </a>
                @endcan
            @endif

            {{-- Restaurar (solo si está eliminado, con soft deletes) --}}
            @if ($isTrashed && $restorePermission)
                @can($restorePermission)
                    <button
                        type="button"
                        wire:click="confirmRestore({{ $row->id }})"
                        @click="close()"
                        class="flex w-full items-center gap-x-2 px-4 py-2 text-sm text-green-600 hover:bg-green-50 dark:text-green-400 dark:hover:bg-green-950"
                    >
                        <x-ui.icon name="rotate-ccw" class="size-4 shrink-0" />
                        {{ __('table.actions.restore') }}
                    </button>
                @endcan

            {{-- Eliminar --}}
            @elseif (! $isTrashed && $deletePermission)
                @can($deletePermission)
                    <button
                        type="button"
                        wire:click="confirmDelete({{ $row->id }})"
                        @click="close()"
                        class="flex w-full items-center gap-x-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950"
                    >
                        <x-ui.icon name="trash-2" class="size-4 shrink-0" />
                        {{ __('table.actions.delete') }}
                    </button>
                @endcan
            @endif
        </div>
    </template>
</div>
