<?php

namespace App\Livewire\App\Personal\User;

use App\Models\User;
use App\Notifications\UserDeletedNotification;
use App\Services\NotificationsService;
use App\Traits\Livewire\HasSoftDeletes;
use App\Traits\Livewire\HasTable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Table extends Component
{
    use HasSoftDeletes, HasTable, Interactions;

    protected string $modelClass = User::class;

    protected string $deletePermission = 'eliminar usuarios';

    protected string $restorePermission = 'restaurar usuarios';

    protected string $modelLabel = 'Usuario';

    /** Búsqueda manual (incluye la relación profile) en render(); el trait no la generaliza. */
    protected function filterable(): array
    {
        return ['email' => 'ilike'];
    }

    protected function defaultSort(): array
    {
        return ['column' => 'username', 'direction' => 'asc'];
    }

    public function softDelete(string|int $id): void
    {
        $this->authorize($this->deletePermission);

        $user = User::find($id);

        // El `if` sin else escondía dos casos distintos —la fila ya no existe,
        // o el usuario está protegido— bajo el mismo silencio. Sin cambio ni
        // aviso, el usuario se va convencido de que borró.
        if ($user === null) {
            $this->toast()->error(__('app.error'), __('app.not_found', ['model' => $this->modelLabel]))->send();

            return;
        }

        if ($user->is_protected) {
            $this->toast()->error(__('app.error'), __('app.user_protected'))->send();

            return;
        }

        $userName = $user->name;
        $user->delete();
        NotificationsService::fire('user_deleted', new UserDeletedNotification($userName));
        $this->toast()->success(__('app.success'), __('app.soft_deleted', ['model' => $this->modelLabel]))->send();
    }

    public function render(): Factory|View
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
            ? User::withTrashed()->where('id', '!=', auth()->id())->where('is_protected', false)
            : User::where('id', '!=', auth()->id())->where('is_protected', false);

        $query = $query
            ->with(['roles', 'profile.media'])
            ->withCount('permissions')
            ->when($this->search, fn ($q) => $q->where(function ($q): void {
                $q->where('username', 'ilike', "%{$this->search}%")
                    ->orWhere('email', 'ilike', "%{$this->search}%")
                    ->orWhereHas('profile', fn ($q) => $q
                        ->where('first_name', 'ilike', "%{$this->search}%")
                        ->orWhere('last_name', 'ilike', "%{$this->search}%")
                    );
            }));

        // El trait aplica el filtro de email y el orden.
        $users = $this->applyTableQuery($query)->paginate($this->quantity);

        $users->getCollection()->transform(function (User $user): User {
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

        return view('app.personal.users._index', ['headers' => $headers, 'users' => $users]);
    }
}
