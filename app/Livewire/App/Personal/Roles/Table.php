<?php

namespace App\Livewire\App\Personal\Roles;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use TallStackUi\Traits\Interactions;

class Table extends Component
{
    use Interactions, WithPagination;

    public string $search   = '';
    public int    $quantity = 25;
    public array  $sort     = ['column' => 'name', 'direction' => 'asc'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingQuantity(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('eliminar roles');

        $role = Role::findOrFail($id);

        if ($role->users()->count() > 0) {
            $this->toast()->error('No se puede eliminar', "El rol \"{$role->display_name}\" tiene usuarios asignados.")->send();
            return;
        }

        $this->dialog()
            ->question('¿Eliminar rol?', "Se eliminará el rol \"{$role->display_name}\" de forma permanente.")
            ->confirm('Eliminar', 'delete', $id)
            ->cancel('Cancelar')
            ->send();
    }

    public function delete(int $id): void
    {
        $this->authorize('eliminar roles');

        $role = Role::find($id);

        if (! $role || $role->users()->count() > 0) {
            return;
        }

        $role->delete();

        $this->toast()->success('Rol eliminado', 'El rol fue eliminado correctamente.')->send();
    }

    public function render()
    {
        $headers = [
            ['index' => 'display_name', 'label' => 'Nombre'],
            ['index' => 'name',         'label' => 'Identificador', 'sortable' => false],
            ['index' => 'permissions',  'label' => 'Permisos',      'sortable' => false],
            ['index' => 'users',        'label' => 'Usuarios',      'sortable' => false],
            ['index' => 'action',       'label' => 'Acciones',      'sortable' => false],
        ];

        $roles = Role::withCount(['permissions', 'users'])
            ->when($this->search, fn ($q) => $q
                ->where('name', 'ilike', "%{$this->search}%")
                ->orWhere('display_name', 'ilike', "%{$this->search}%")
            )
            ->orderBy($this->sort['column'], $this->sort['direction'])
            ->paginate($this->quantity);

        return view('app.personal.roles._index', compact('headers', 'roles'));
    }
}
