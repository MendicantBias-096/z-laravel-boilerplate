<div>
    {{-- Toolbar --}}
    <div class="mb-4 flex items-center justify-between gap-4">
        <x-ts-input
            wire:model.live.debounce.300ms="search"
            placeholder="Buscar usuarios..."
            class="w-full max-w-sm"
        />

        @can('crear usuarios')
            <a href="{{ route('personal.usuarios.create') }}" wire:navigate
               class="inline-flex shrink-0 items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-white"
               style="background: linear-gradient(135deg, #f53003 0%, #c0392b 100%);">
                <x-ui.icon name="plus" class="size-4" />
                Nuevo usuario
            </a>
        @endcan
    </div>

    {{-- Tabla --}}
    <x-ts-card>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-left dark:border-white/10">
                        <th class="pb-3 pr-4 font-medium text-content-muted">
                            <button wire:click="sort('name')" class="flex items-center gap-1 hover:text-content">
                                Nombre
                                @if ($sortField === 'name')
                                    <x-ui.icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="size-3" />
                                @endif
                            </button>
                        </th>
                        <th class="pb-3 pr-4 font-medium text-content-muted">
                            <button wire:click="sort('email')" class="flex items-center gap-1 hover:text-content">
                                Correo
                                @if ($sortField === 'email')
                                    <x-ui.icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="size-3" />
                                @endif
                            </button>
                        </th>
                        <th class="pb-3 pr-4 font-medium text-content-muted">Rol</th>
                        <th class="pb-3 pr-4 font-medium text-content-muted">Estado</th>
                        <th class="pb-3 font-medium text-content-muted">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    @forelse ($users as $user)
                        <tr class="{{ $user->trashed() ? 'opacity-50' : '' }}">
                            <td class="py-3 pr-4 font-medium text-content">{{ $user->name }}</td>
                            <td class="py-3 pr-4 text-content-muted">{{ $user->email }}</td>
                            <td class="py-3 pr-4 text-content-muted">{{ $user->roles->first()?->name ?? '—' }}</td>
                            <td class="py-3 pr-4">
                                @if ($user->trashed())
                                    <x-ts-badge text="Eliminado" color="red" />
                                @else
                                    <x-ts-badge text="Activo" color="green" />
                                @endif
                            </td>
                            <td class="py-3">
                                <div class="flex items-center gap-2">
                                    @if (! $user->trashed())
                                        @can('editar usuarios')
                                            <a href="{{ route('personal.usuarios.edit', $user) }}" wire:navigate
                                               class="text-sm text-blue-600 hover:underline dark:text-blue-400">
                                                Editar
                                            </a>
                                        @endcan
                                    @endif

                                    @if ($user->trashed())
                                        @can('restaurar usuarios')
                                            <button wire:click="restore({{ $user->id }})"
                                                    wire:confirm="¿Restaurar este usuario?"
                                                    class="text-sm text-green-600 hover:underline dark:text-green-400">
                                                Restaurar
                                            </button>
                                        @endcan
                                    @else
                                        @can('eliminar usuarios')
                                            <button wire:click="softDelete({{ $user->id }})"
                                                    wire:confirm="¿Eliminar este usuario?"
                                                    class="text-sm text-red-600 hover:underline dark:text-red-400">
                                                Eliminar
                                            </button>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-content-muted">
                                No se encontraron usuarios.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <x-slot:footer>
                {{ $users->links() }}
            </x-slot:footer>
        @endif
    </x-ts-card>
</div>
