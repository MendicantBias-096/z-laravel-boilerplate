<div x-data="{ showFilters: false }">

    {{-- ── Barra superior ──────────────────────────────────────────────── --}}
    <div class="mb-3 flex items-center gap-2">

        {{-- Búsqueda --}}
        <div class="relative w-64">
            <x-ui.icon name="search"
                class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-content-subtle" />
            <input
                wire:model.live.debounce.400ms="search"
                type="search"
                placeholder="Buscar..."
                class="w-full rounded-lg border border-line bg-panel py-2 pl-9 pr-4 text-sm text-content placeholder-content-subtle focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:bg-panel"
            />
        </div>

        {{-- Botón filtros --}}
        <button
            type="button"
            @click="showFilters = !showFilters"
            :class="showFilters || @js($filterEmail || $filterRole)
                ? 'border-primary-500 bg-primary-50 text-primary-600 dark:bg-primary-950 dark:text-primary-400'
                : 'border-line bg-panel text-content-muted hover:bg-panel-alt hover:text-content dark:bg-panel'"
            class="inline-flex cursor-pointer items-center gap-1.5 rounded-lg border px-3 py-2 text-sm transition-colors"
        >
            <x-ui.icon name="sliders-horizontal" class="size-4" />
            Filtros
            @if ($filterEmail || $filterRole)
                <span class="flex size-2 rounded-full bg-primary-500"></span>
            @endif
        </button>

        {{-- Separador --}}
        <div class="flex-1"></div>

        {{-- Botón nueva acción --}}
        @can('crear usuarios')
            <a href="{{ route('personal.usuarios.create') }}" wire:navigate
               class="inline-flex shrink-0 items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-white"
               style="background: linear-gradient(135deg, #f53003 0%, #c0392b 100%);">
                <x-ui.icon name="plus" class="size-4" />
                Nuevo usuario
            </a>
        @endcan
    </div>

    {{-- ── Panel de filtros ────────────────────────────────────────────── --}}
    <div
        x-show="showFilters"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        x-cloak
        class="mb-4 overflow-hidden rounded-lg border border-line bg-panel shadow-sm"
    >
        {{-- Cabecera del panel --}}
        <div class="flex items-center justify-between border-b border-line bg-panel-alt px-4 py-2.5">
            <div class="flex items-center gap-2 text-sm font-medium text-content-muted">
                <x-ui.icon name="sliders-horizontal" class="size-3.5" />
                Filtros
            </div>
            @php $activeCount = (int) (bool) $filterEmail + (int) (bool) $filterRole; @endphp
            @if ($activeCount)
                <span class="inline-flex items-center rounded-full bg-primary-100 px-2 py-0.5 text-xs font-medium text-primary-700 dark:bg-primary-950 dark:text-primary-300">
                    {{ $activeCount }} {{ Str::plural('activo', $activeCount) }}
                </span>
            @endif
        </div>

        {{-- Campos --}}
        <div class="flex flex-wrap items-end gap-3 p-4">
            <x-ui.ts-table.filter-input
                label="Correo"
                icon="search"
                wire:model.live.debounce.400ms="filterEmail"
                placeholder="Filtrar por correo..."
            />

            <x-ui.ts-table.filter-select
                label="Rol"
                wire:model.live="filterRole"
                placeholder="Todos los roles"
                :options="$roles"
            />

            @if ($filterEmail || $filterRole)
                <div class="group relative self-end">
                    <button
                        type="button"
                        wire:click="clearFilters"
                        class="flex cursor-pointer items-center justify-center rounded-md border border-red-200 bg-red-50 p-2 text-red-600 transition-colors hover:border-red-300 hover:bg-red-100 dark:border-red-800 dark:bg-red-950 dark:text-red-400 dark:hover:border-red-700 dark:hover:bg-red-900"
                    >
                        <x-ui.icon name="rotate-ccw" class="size-3.5" />
                    </button>
                    <div class="pointer-events-none absolute bottom-full left-1/2 mb-2 -translate-x-1/2 whitespace-nowrap rounded-md bg-gray-800 px-2 py-1 text-xs text-white opacity-0 transition-opacity duration-150 group-hover:opacity-100 dark:bg-dark-600">
                        Limpiar filtros
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ── Tabla ────────────────────────────────────────────────────────── --}}
    <x-ts-table
        :headers="$headers"
        :rows="$users"
        :sort="$sort"
        striped
    >
        @interact('column_photo', $row)
            <div class="flex justify-center">
                @if ($row->profile?->photo_url)
                    <img
                        src="{{ $row->profile->photo_url }}"
                        alt="{{ $row->username }}"
                        class="h-8 w-8 flex-shrink-0 rounded-full object-cover"
                    >
                @else
                    <div
                        class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full text-xs font-bold text-white"
                        style="background: linear-gradient(135deg, #f53003 0%, #c0392b 100%);"
                    >
                        {{ mb_strtoupper(mb_substr($row->username, 0, 1)) }}
                    </div>
                @endif
            </div>
        @endinteract

        @interact('column_status', $row)
            @if ($row->trashed())
                <x-ts-badge text="Eliminado" color="red" />
            @else
                <x-ts-badge text="Activo" color="green" />
            @endif
        @endinteract

        @interact('column_action', $row)
            <x-ui.ts-table.actions
                :row="$row"
                edit-route="personal.usuarios.edit"
                edit-permission="editar usuarios"
                delete-permission="eliminar usuarios"
                restore-permission="restaurar usuarios"
                model="usuario"
            />
        @endinteract
    </x-ts-table>

    {{-- ── Footer: conteo + paginador ──────────────────────────────────── --}}
    @if ($users->total() > 0)
        <div class="mt-4 flex flex-wrap items-center justify-between gap-3">

            {{-- Selector de cantidad --}}
            <div class="flex items-center gap-1.5 text-sm text-content-muted">
                Mostrando
                <select
                    wire:model.live="quantity"
                    class="rounded-md border border-line bg-panel px-2 py-1 text-sm text-content focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                >
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                resultados de {{ $users->total() }}
            </div>

            {{-- Paginador --}}
            @if ($users->hasPages())
                {{ $users->links('tallstack-ui::components.table.paginators') }}
            @endif

        </div>
    @endif

</div>
