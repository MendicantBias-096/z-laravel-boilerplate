<?php

namespace App\Livewire\App\Personal\Roles;

use Livewire\Attributes\Computed;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use TallStackUi\Traits\Interactions;

class Form extends Component
{
    use Interactions;

    public ?Role $record = null;

    public string $display_name   = '';
    public string $name           = '';   // slug — generado automáticamente
    public array  $permissionList = [];

    public function mount(): void
    {
        if ($this->record) {
            $this->display_name   = $this->record->display_name ?? $this->record->name;
            $this->name           = $this->record->name;
            $this->permissionList = $this->record->permissions->pluck('name')->toArray();
        }
    }

    public function updatedDisplayName(string $value): void
    {
        $this->name = \Illuminate\Support\Str::slug($value);
    }

    /**
     * Permisos organizados por grupo → módulo desde config/roles.php.
     *
     * @return array<string, array<string, list<string>>>
     */
    #[Computed]
    public function permissionsByGroup(): array
    {
        $groups     = config('roles.module_groups', []);
        $allModules = config('roles.permissions', []);
        $result     = [];

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
        $missing     = array_diff($permissions, $this->permissionList);

        if (empty($missing)) {
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
            && empty(array_diff($permissions, $this->permissionList));
    }

    public function save(): void
    {
        $isEdit = $this->record !== null;

        $this->validate([
            'display_name' => ['required', 'string', 'max:100'],
            'name'         => ['required', 'string', 'max:100',
                $isEdit
                    ? \Illuminate\Validation\Rule::unique('roles', 'name')->ignore($this->record->id)
                    : \Illuminate\Validation\Rule::unique('roles', 'name'),
            ],
            'permissionList'   => ['array'],
            'permissionList.*' => ['string', 'exists:permissions,name'],
        ]);

        $role = Role::updateOrCreate(
            ['id' => $this->record?->id],
            ['name' => $this->name, 'display_name' => $this->display_name, 'guard_name' => 'web']
        );

        $role->syncPermissions($this->permissionList);

        $this->toast()
            ->success('Éxito', $isEdit ? 'Rol actualizado correctamente.' : 'Rol creado correctamente.')
            ->flash()
            ->send();

        $this->redirect(route('personal.roles.index'), navigate: true);
    }

    public function render()
    {
        return view('app.personal.roles._form');
    }
}
