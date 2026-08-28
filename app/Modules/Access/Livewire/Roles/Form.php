<?php

namespace App\Modules\Access\Livewire\Roles;

use App\Modules\Access\Models\Role;
use App\Modules\Platform\Services\NotificationsService;
use App\Modules\Access\Notifications\RoleCreatedNotification;
use App\Modules\Access\Notifications\RoleUpdatedNotification;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Form extends Component
{
    use Interactions;

    public ?Role $record = null;

    public string $display_name = '';

    public string $name = '';   // slug — generado automáticamente

    public array $permissionList = [];

    public function mount(): void
    {
        if ($this->record instanceof Role) {
            $this->display_name = $this->record->display_name ?? $this->record->name;
            $this->name = $this->record->name;
            $this->permissionList = $this->record->permissions->pluck('name')->toArray();
        }
    }

    public function updatedDisplayName(string $value): void
    {
        $this->name = Str::slug($value);
    }

    /**
     * Permisos organizados por grupo → módulo desde config/roles.php.
     *
     * @return array<string, array<string, list<string>>>
     */
    #[Computed]
    public function permissionsByGroup(): array
    {
        $groups = config('roles.module_groups', []);
        $allModules = config('roles.permissions', []);
        $result = [];

        foreach ($groups as $group => $modules) {
            foreach ($modules as $module) {
                if (isset($allModules[$module])) {
                    $result[$group][$module] = $allModules[$module];
                }
            }
        }

        // Módulos sin grupo asignado → "other"
        $grouped = array_merge(...array_values($groups));
        foreach ($allModules as $module => $permissions) {
            if (! in_array($module, $grouped)) {
                $result['other'][$module] = $permissions;
            }
        }

        return $result;
    }

    /**
     * Toggle de todos los permisos de un módulo.
     */
    public function toggleModule(string $module): void
    {
        $permissions = config("roles.permissions.{$module}", []);
        $missing = array_diff($permissions, $this->permissionList);

        if ($missing === []) {
            $this->permissionList = array_values(array_diff($this->permissionList, $permissions));
        } else {
            $this->permissionList = array_values(array_unique(array_merge($this->permissionList, $permissions)));
        }
    }

    /**
     * Indica si todos los permisos de un módulo están seleccionados.
     */
    public function moduleFullySelected(string $module): bool
    {
        $permissions = config("roles.permissions.{$module}", []);

        return count($permissions) > 0
            && array_diff($permissions, $this->permissionList) === [];
    }

    public function save(): void
    {
        $isEdit = $this->record instanceof Role;

        $this->validate([
            'display_name' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:100',
                $isEdit
                    ? Rule::unique('roles', 'name')->ignore($this->record->id)
                    : Rule::unique('roles', 'name'),
            ],
            'permissionList' => ['array'],
            'permissionList.*' => ['string', 'exists:permissions,name'],
        ]);

        $role = Role::updateOrCreate(
            ['id' => $this->record?->id],
            ['name' => $this->name, 'display_name' => $this->display_name, 'guard_name' => 'web']
        );

        $role->syncPermissions($this->permissionList);

        $roleName = $role->display_name ?? $role->name;
        if ($isEdit) {
            NotificationsService::fire('role_updated', new RoleUpdatedNotification($roleName));
        } else {
            NotificationsService::fire('role_created', new RoleCreatedNotification($roleName));
        }

        $this->toast()
            ->success('Éxito', $isEdit ? 'Rol actualizado correctamente.' : 'Rol creado correctamente.')
            ->flash()
            ->send();

        $this->redirect(route('access.roles.index'), navigate: true);
    }

    public function render(): Factory|View
    {
        return view('access::roles._form');
    }
}
