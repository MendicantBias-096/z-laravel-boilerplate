<?php

namespace App\Livewire\App\Personal\User;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class Table extends Component
{
    use Interactions, WithPagination;

    public string $search = '';

    public int $quantity = 25;

    public array $sort = ['column' => 'name', 'direction' => 'asc'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingQuantity(): void
    {
        $this->resetPage();
    }

    public function softDelete(int $id): void
    {
        $this->authorize('eliminar usuarios');

        User::find($id)?->delete();

        $this->toast()->success('Éxito', 'Usuario eliminado correctamente.')->send();
    }

    public function restore(int $id): void
    {
        $this->authorize('restaurar usuarios');

        User::withTrashed()->find($id)?->restore();

        $this->toast()->success('Éxito', 'Usuario restaurado correctamente.')->send();
    }

    public function render()
    {
        $headers = [
            ['index' => 'name',  'label' => 'Nombre'],
            ['index' => 'email', 'label' => 'Correo'],
            ['index' => 'role',  'label' => 'Rol', 'sortable' => false],
            ['index' => 'status', 'label' => 'Estado', 'sortable' => false],
            ['index' => 'action', 'label' => 'Acciones', 'sortable' => false],
        ];

        $users = User::withTrashed()
            ->with('roles')
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'ilike', "%{$this->search}%")
                  ->orWhere('email', 'ilike', "%{$this->search}%");
            }))
            ->orderBy($this->sort['column'], $this->sort['direction'])
            ->paginate($this->quantity);

        // Append virtual fields
        $users->getCollection()->transform(function (User $user) {
            $user->role   = $user->roles->first()?->name ?? '—';
            $user->status = $user->trashed() ? 'Eliminado' : 'Activo';

            return $user;
        });

        return view('app.personal.users._index', compact('headers', 'users'));
    }
}
