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

    public string $sortField = 'name';

    public string $sortDirection = 'asc';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function sort(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
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
        $users = User::withTrashed()
            ->with('roles')
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'ilike', "%{$this->search}%")
                  ->orWhere('email', 'ilike', "%{$this->search}%");
            }))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(25);

        return view('app.personal.users._index', compact('users'));
    }
}
