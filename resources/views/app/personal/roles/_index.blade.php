<div>

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

        <div class="flex-1"></div>

        @can('crear roles')
            <a href="{{ route('personal.roles.create') }}" wire:navigate
               class="inline-flex shrink-0 items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-white"
               style="background: linear-gradient(135deg, #f53003 0%, #c0392b 100%);">
                <x-ui.icon name="plus" class="size-4" />
                Nuevo rol
            </a>
        @endcan
    </div>

    {{-- ── Tabla ────────────────────────────────────────────────────────── --}}
    <x-ts-table
        :headers="$headers"
        :rows="$roles"
        :sort="$sort"
        striped
    >
        @interact('column_name', $row)
            <code class="rounded bg-panel-alt px-2 py-0.5 font-mono text-xs text-content-subtle">{{ $row->name }}</code>
        @endinteract

        @interact('column_permissions', $row)
            <x-ts-badge :text="$row->permissions_count . ' ' . Str::plural('permiso', $row->permissions_count)" color="blue" />
        @endinteract

        @interact('column_users', $row)
            <span class="text-sm text-content-muted">{{ $row->users_count }}</span>
        @endinteract

        @interact('column_action', $row)
            <x-ui.ts-table.actions
                :row="$row"
                edit-route="personal.roles.edit"
                edit-permission="editar roles"
                delete-permission="eliminar roles"
                model="rol"
                :soft-deletes="false"
            />
        @endinteract
    </x-ts-table>

    {{-- ── Footer: conteo + paginador ──────────────────────────────────── --}}
    @if ($roles->total() > 0)
        <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
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
                resultados de {{ $roles->total() }}
            </div>

            @if ($roles->hasPages())
                {{ $roles->links('tallstack-ui::components.table.paginators') }}
            @endif
        </div>
    @endif

</div>
