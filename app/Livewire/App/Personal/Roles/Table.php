<?php

namespace App\Livewire\App\Personal\Roles;

use App\Notifications\RoleDeletedNotification;
use App\Services\NotificationsService;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Role;
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
            $this->toast()->error(__('app.role_delete_error'), __('app.role_delete_has_users', ['name' => $role->display_name]))->send();
            return;
        }

        $this->dialog()
            ->question(__('app.role_delete_title'), __('app.role_delete_desc', ['name' => $role->display_name]))
            ->confirm(__('app.role_delete_confirm'), 'delete', $id)
            ->cancel(__('app.role_delete_cancel'))
            ->send();
    }

    public function delete(int $id): void
    {
        $this->authorize('eliminar roles');

        $role = Role::find($id);

        if (! $role || $role->users()->count() > 0) {
            return;
        }

        $roleName = $role->display_name ?? $role->name;
        $role->delete();

        NotificationsService::fire('role_deleted', new RoleDeletedNotification($roleName));

        $this->toast()->success(__('app.role_deleted'), __('app.role_deleted_desc'))->send();
    }

    public function render()
    {
        $headers = [
            ['index' => 'display_name', 'label' => __('table.roles.headers.name')],
            ['index' => 'name',         'label' => __('table.roles.headers.identifier'),  'sortable' => false],
            ['index' => 'permissions',  'label' => __('table.roles.headers.permissions'), 'sortable' => false],
            ['index' => 'users',        'label' => __('table.roles.headers.users'),       'sortable' => false],
            ['index' => 'action',       'label' => __('table.roles.headers.actions'),     'sortable' => false],
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
