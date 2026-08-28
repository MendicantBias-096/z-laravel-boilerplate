<?php

namespace App\Modules\Access\Livewire\Roles;

use App\Modules\Access\Models\Role;
use App\Modules\Access\Notifications\RoleDeletedNotification;
use App\Modules\Platform\Services\NotificationsService;
use App\Modules\Platform\Traits\Livewire\HasTable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Table extends Component
{
    use HasTable, Interactions;

    protected function searchable(): array
    {
        return ['name', 'display_name'];
    }

    protected function defaultSort(): array
    {
        return ['column' => 'name', 'direction' => 'asc'];
    }

    public function confirmDelete(string|int $id): void
    {
        $this->authorize('eliminar roles');

        $role = Role::findOrFail($id);

        if ($role->is_protected) {
            $this->toast()->error(__('platform::app.role_delete_error'), __('platform::app.role_delete_protected', ['name' => $role->display_name]))->send();

            return;
        }

        if ($role->users()->count() > 0) {
            $this->toast()->error(__('platform::app.role_delete_error'), __('platform::app.role_delete_has_users', ['name' => $role->display_name]))->send();

            return;
        }

        $this->dialog()
            ->question(__('platform::app.role_delete_title'), __('platform::app.role_delete_desc', ['name' => $role->display_name]))
            ->confirm(__('platform::app.role_delete_confirm'), 'delete', $id)
            ->cancel(__('platform::app.role_delete_cancel'))
            ->send();
    }

    public function delete(string|int $id): void
    {
        $this->authorize('eliminar roles');

        $role = Role::find($id);

        // Dos causas distintas, y el usuario necesita distinguirlas: una la
        // arregla él quitando usuarios del rol, la otra no. Antes las dos
        // acababan en el mismo `return` mudo y el diálogo se cerraba como si
        // hubiera funcionado.
        if ($role === null) {
            $this->toast()->error(__('platform::app.error'), __('platform::app.not_found', ['model' => 'Rol']))->send();

            return;
        }

        if ($role->is_protected) {
            $this->toast()->error(__('platform::app.role_delete_error'), __('platform::app.role_delete_protected', ['name' => $role->display_name]))->send();

            return;
        }

        if ($role->users()->count() > 0) {
            $this->toast()->error(__('platform::app.role_delete_error'), __('platform::app.role_delete_has_users', ['name' => $role->display_name]))->send();

            return;
        }

        $roleName = $role->display_name ?? $role->name;
        $role->delete();

        NotificationsService::fire('role_deleted', new RoleDeletedNotification($roleName));

        $this->toast()->success(__('platform::app.role_deleted'), __('platform::app.role_deleted_desc'))->send();
    }

    public function render(): Factory|View
    {
        $headers = [
            ['index' => 'display_name', 'label' => __('platform::table.roles.headers.name')],
            ['index' => 'name',         'label' => __('platform::table.roles.headers.identifier'),  'sortable' => false],
            ['index' => 'permissions',  'label' => __('platform::table.roles.headers.permissions'), 'sortable' => false],
            ['index' => 'users',        'label' => __('platform::table.roles.headers.users'),       'sortable' => false],
            ['index' => 'action',       'label' => __('platform::table.roles.headers.actions'),     'sortable' => false],
        ];

        $roles = $this->applyTableQuery(Role::withCount(['permissions', 'users']))
            ->paginate($this->quantity);

        return view('access::roles._index', ['headers' => $headers, 'roles' => $roles]);
    }
}
