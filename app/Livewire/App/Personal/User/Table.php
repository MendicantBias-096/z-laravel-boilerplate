<?php

namespace App\Livewire\App\Personal\User;

use App\Models\User;
use App\Traits\Livewire\HasSoftDeletes;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use TallStackUi\Traits\Interactions;

class Table extends Component
{
    use Interactions, WithPagination, HasSoftDeletes;

    protected string $modelClass = User::class;
    protected string $deletePermission = 'eliminar usuarios';
    protected string $restorePermission = 'restaurar usuarios';
    protected string $modelLabel = 'Usuario';

    public string $search = '';
    public string $filterEmail = '';
    public string $filterRole = '';

    public int $quantity = 25;

    public array $sort = ['column' => 'username', 'direction' => 'asc'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingQuantity(): void
    {
        $this->resetPage();
    }

    public function updatingFilterEmail(): void
    {
        $this->resetPage();
    }

    public function updatingFilterRole(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset('filterEmail', 'filterRole');
        $this->resetPage();
    }

    public function render()
    {
        $headers = [
            ['index' => 'username', 'label' => 'Usuario'],
            ['index' => 'name',     'label' => 'Nombre', 'sortable' => false],
            ['index' => 'email',    'label' => 'Correo'],
            ['index' => 'role',   'label' => 'Rol', 'sortable' => false],
            ['index' => 'status', 'label' => 'Estado', 'sortable' => false],
            ['index' => 'action', 'label' => 'Acciones', 'sortable' => false],
        ];

        $users = User::withTrashed()
            ->with(['roles', 'profile'])
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('username', 'ilike', "%{$this->search}%")
                  ->orWhere('email', 'ilike', "%{$this->search}%")
                  ->orWhereHas('profile', fn ($q) => $q
                      ->where('first_name', 'ilike', "%{$this->search}%")
                      ->orWhere('last_name', 'ilike', "%{$this->search}%")
                  );
            }))
            ->when($this->filterEmail, fn ($q) => $q->where('email', 'ilike', "%{$this->filterEmail}%"))
            ->when($this->filterRole, fn ($q) => $q->whereHas('roles', fn ($q) => $q->where('name', $this->filterRole)))
            ->orderBy($this->sort['column'], $this->sort['direction'])
            ->paginate($this->quantity);

        $users->getCollection()->transform(function (User $user) {
            $user->role   = $user->roles->first()?->name ?? '—';
            $user->status = $user->trashed() ? 'Eliminado' : 'Activo';

            return $user;
        });

        $roles = Role::orderBy('name')->pluck('name', 'name')->toArray();

        return view('app.personal.users._index', compact('headers', 'users', 'roles'));
    }
}
