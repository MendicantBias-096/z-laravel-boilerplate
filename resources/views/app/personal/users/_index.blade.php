<div>
    <div class="mb-4 flex justify-end">
        @can('crear usuarios')
            <a href="{{ route('personal.usuarios.create') }}" wire:navigate
               class="inline-flex shrink-0 items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-white"
               style="background: linear-gradient(135deg, #f53003 0%, #c0392b 100%);">
                <x-ui.icon name="plus" class="size-4" />
                Nuevo usuario
            </a>
        @endcan
    </div>

    <x-ts-table
        :headers="$headers"
        :rows="$users"
        :sort="$sort"
        :filter="true"
        :quantity="[10, 25, 50, 100]"
        paginate
        striped
    >
        @interact('column_status', $row)
            @if ($row->trashed())
                <x-ts-badge text="Eliminado" color="red" />
            @else
                <x-ts-badge text="Activo" color="green" />
            @endif
        @endinteract

        @interact('column_action', $row)
            <div class="flex items-center gap-3">
                @if (! $row->trashed())
                    @can('editar usuarios')
                        <a href="{{ route('personal.usuarios.edit', $row) }}" wire:navigate
                           class="text-sm text-blue-600 hover:underline dark:text-blue-400">
                            Editar
                        </a>
                    @endcan
                @endif

                @if ($row->trashed())
                    @can('restaurar usuarios')
                        <button wire:click="restore({{ $row->id }})"
                                wire:confirm="¿Restaurar este usuario?"
                                class="text-sm text-green-600 hover:underline dark:text-green-400">
                            Restaurar
                        </button>
                    @endcan
                @else
                    @can('eliminar usuarios')
                        <button wire:click="softDelete({{ $row->id }})"
                                wire:confirm="¿Eliminar este usuario?"
                                class="text-sm text-red-600 hover:underline dark:text-red-400">
                            Eliminar
                        </button>
                    @endcan
                @endif
            </div>
        @endinteract
    </x-ts-table>
</div>
