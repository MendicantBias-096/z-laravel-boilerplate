<?php

namespace App\Livewire\App\Personal\User;

use App\Models\User;
use App\Notifications\UserDeletedNotification;
use App\Services\NotificationsService;
use App\Traits\Livewire\HasSoftDeletes;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class Table extends Component
{
    use Interactions, WithPagination, HasSoftDeletes;

    protected string $modelClass       = User::class;
    protected string $deletePermission = 'eliminar usuarios';
    protected string $restorePermission = 'restaurar usuarios';
    protected string $modelLabel       = 'Usuario';

    public function softDelete(int $id): void
    {
        $this->authorize($this->deletePermission);

        $user = User::find($id);
        if ($user) {
            $userName = $user->name;
            $user->delete();
            NotificationsService::fire('user_deleted', new UserDeletedNotification($userName));
            $this->toast()->success(__('app.success'), __('app.soft_deleted', ['model' => $this->modelLabel]))->send();
        }
    }

    public string $search      = '';
    public string $filterEmail = '';

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

    public function clearFilters(): void
    {
        $this->reset('filterEmail');
        $this->resetPage();
    }

    public function render()
    {
        $headers = [
            ['index' => 'photo',       'label' => '',                                        'sortable' => false],
            ['index' => 'username',    'label' => __('table.users.headers.username')],
            ['index' => 'name',        'label' => __('table.users.headers.name'),            'sortable' => false],
            ['index' => 'email',       'label' => __('table.users.headers.email')],
            ['index' => 'role',        'label' => __('table.users.headers.role'),             'sortable' => false],
            ['index' => 'permissions', 'label' => __('table.users.headers.permissions'),     'sortable' => false],
            ['index' => 'status',      'label' => __('table.users.headers.status'),          'sortable' => false],
            ['index' => 'action',      'label' => __('table.users.headers.actions'),         'sortable' => false],
        ];

        $canRestore = auth()->user()->can('restaurar usuarios');

        $query = $canRestore
            ? User::withTrashed()->where('id', '!=', auth()->id())
            : User::where('id', '!=', auth()->id());

        $users = $query
            ->with(['roles', 'profile.media'])
            ->withCount('permissions')
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('username', 'ilike', "%{$this->search}%")
                  ->orWhere('email', 'ilike', "%{$this->search}%")
                  ->orWhereHas('profile', fn ($q) => $q
                      ->where('first_name', 'ilike', "%{$this->search}%")
                      ->orWhere('last_name', 'ilike', "%{$this->search}%")
                  );
            }))
            ->when($this->filterEmail, fn ($q) => $q->where('email', 'ilike', "%{$this->filterEmail}%"))
            ->orderBy($this->sort['column'], $this->sort['direction'])
            ->paginate($this->quantity);

        $users->getCollection()->transform(function (User $user) {
            $user->role = $user->roles->first()?->display_name ?? $user->roles->first()?->name ?? '—';

            if ($user->trashed()) {
                $user->status = __('table.users.status_deleted');
            } elseif (! $user->is_active) {
                $user->status = __('table.users.status_inactive');
            } else {
                $user->status = __('table.users.status_active');
            }

            return $user;
        });

        return view('app.personal.users._index', compact('headers', 'users'));
    }
}
