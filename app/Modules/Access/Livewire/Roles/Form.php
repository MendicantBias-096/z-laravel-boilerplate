<?php

namespace App\Modules\Access\Livewire\Roles;

use App\Modules\Access\Models\Role;
use App\Modules\Access\Models\User;
use App\Modules\Access\Notifications\RoleCreatedNotification;
use App\Modules\Access\Notifications\RoleUpdatedNotification;
use App\Modules\Access\Traits\Livewire\WithPermissionMatrix;
use App\Modules\Platform\Services\NotificationsService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Form extends Component
{
    use Interactions;
    use WithPermissionMatrix;

    #[Locked]
    public ?Role $record = null;

    public string $display_name = '';

    public string $name = '';   // slug — identificador, se fija al crear

    public array $permissionList = [];

    public function mount(): void
    {
        if ($this->record instanceof Role) {
            $this->display_name = $this->record->display_name ?? $this->record->name;
            $this->name = $this->record->name;
            $this->permissionList = $this->record->permissions->pluck('name')->toArray();
        }
    }

    /**
     * El slug se calcula al crear y ya no se vuelve a tocar.
     *
     * `name` es con lo que el código pregunta por el rol —`Roles::ADMIN`, el
     * seeder, un middleware— y `model_has_roles` guarda `role_id`, así que
     * renombrar no desasigna a nadie: rompe el otro lado. Renombrar
     * «Administrador» dejaría a `Gate::before` buscando un `admin` que ya no
     * existe, y todos los administradores perderían sus privilegios sin un
     * error. La etiqueta se edita libremente; el identificador no.
     */
    public function updatedDisplayName(string $value): void
    {
        if ($this->record instanceof Role) {
            return;
        }

        $this->name = Str::slug($value);
    }

    /** Un rol de plataforma se muestra, pero no se edita. */
    #[Computed]
    public function isProtected(): bool
    {
        return $this->record?->is_protected === true;
    }

    public function save(): void
    {
        $isEdit = $this->record instanceof Role;

        // La ruta no interviene en una petición de Livewire, así que la puerta
        // se comprueba aquí. Un rol concentra permisos: crearlo o editarlo es
        // repartir privilegios.
        $this->authorize($isEdit ? 'access.roles.update' : 'access.roles.create');

        // La vista ya desactiva los campos; esto es lo que de verdad decide.
        // Sin el guard bastaría con quitar el `disabled` desde el navegador
        // para escribir un cambio que el siguiente seed revertiría.
        if ($this->isProtected()) {
            $this->toast()
                ->error(__('platform::app.role_protected_error'), __('platform::app.role_protected_desc'))
                ->send();

            return;
        }

        $this->validate([
            'display_name' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:100',
                $isEdit
                    ? Rule::unique('roles', 'name')->ignore($this->record->id)
                    : Rule::unique('roles', 'name'),
            ],
            'permissionList' => ['array'],
            'permissionList.*' => ['string', Rule::in($this->grantablePermissions())],
        ]);

        // Ver la nota de `UserForm::store()`: el id como atributo de búsqueda
        // entra en el `fill()` del alta, y ahí no es asignable en masa.
        $role = $this->record ?? new Role;

        $role->fill([
            'name' => $this->name,
            'display_name' => $this->display_name,
            'guard_name' => 'web',
        ])->save();

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

    /**
     * Solo se mete en un rol lo que el actor ya tiene. Sin esto, quien puede
     * crear roles se fabrica uno con todos los permisos y se lo asigna.
     *
     * @return list<string>
     */
    private function grantablePermissions(): array
    {
        /** @var User $actor */
        $actor = auth()->user();

        return array_values($actor->getAllPermissions()->pluck('name')->all());
    }

    public function render(): Factory|View
    {
        return view('access::roles._form');
    }
}
