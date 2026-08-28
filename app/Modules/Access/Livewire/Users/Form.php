<?php

namespace App\Modules\Access\Livewire\Users;

use App\Modules\Access\Livewire\Forms\UserForm;
use App\Modules\Access\Models\Role;
use App\Modules\Access\Models\User;
use App\Modules\Access\Notifications\UserCreatedNotification;
use App\Modules\Access\Notifications\UserUpdatedNotification;
use App\Modules\Platform\Services\NotificationsService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use TallStackUi\Traits\Interactions;

class Form extends Component
{
    use Interactions, WithFileUploads;

    #[Locked]
    public ?User $record = null;

    public UserForm $form;

    /** @var TemporaryUploadedFile|null */
    public $photo;

    public array $permissionList = [];

    public array $originalPermissions = [];

    public function mount(): void
    {
        if ($this->record instanceof User) {
            $this->record->load('roles');

            $this->form->fill([
                'id' => $this->record->id,
                'username' => $this->record->username,
                'email' => $this->record->email,
                'first_name' => $this->record->profile?->first_name ?? '',
                'last_name' => $this->record->profile?->last_name ?? '',
                'role' => $this->record->roles->first()?->name,
                'is_active' => $this->record->is_active,
            ]);

            $this->permissionList = $this->record->getAllPermissions()->pluck('name')->toArray();
            $this->originalPermissions = $this->permissionList;
        }
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

    /**
     * Restaura los permisos originales del usuario (antes de aplicar plantillas).
     */
    public function restorePermissions(): void
    {
        $this->permissionList = $this->originalPermissions;
        $this->selectedTemplate = '';

        $this->toast()->info(__('platform::app.user_perms_restored'), __('platform::app.user_perms_restored_desc'))->send();
    }

    /**
     * Al cambiar el rol se cargan sus permisos como plantilla.
     * Solo se dispara en interacción del usuario, no en mount().
     */
    public function updatedFormRole(?string $value): void
    {
        if (! $value) {
            return;
        }

        $role = Role::findByName($value);
        $this->permissionList = $role->permissions->pluck('name')->toArray();

        $templateName = $role->display_name ?? ucfirst($role->name);
        $this->toast()->info(__('platform::app.user_perms_loaded'), __('platform::app.user_perms_loaded_desc', ['name' => $templateName]))->send();
    }

    public function save(): void
    {
        // La ruta ya decidió quién entra, pero la petición de Livewire no pasa
        // por ella: va a /livewire/update. La puerta tiene que estar también
        // aquí, y sobre el registro, no solo sobre el permiso.
        if ($this->record instanceof User) {
            $this->authorize('access.users.update');
            $this->authorize('update', $this->record);
        } else {
            $this->authorize('access.users.create');
        }

        $this->validate(['photo' => ['nullable', 'image', 'max:2048']]);

        $this->form->validate();

        $this->validate([
            'permissionList' => ['array'],
            'permissionList.*' => ['string', Rule::in($this->grantablePermissions())],
            'form.role' => ['nullable', Rule::in($this->grantableRoles())],
        ]);

        $isEdit = $this->form->id !== null;

        $user = $this->form->store();

        if ($this->photo) {
            // `store()` acaba de crear el perfil, pero `$user->profile` lee la
            // relación cacheada: consultarla es lo que garantiza que existe, y
            // si no existiera falla diciéndolo en vez de sobre un null.
            $user->profile()->firstOrFail()->addMedia($this->photo->getRealPath())
                ->usingFileName($this->photo->getClientOriginalName())
                ->toMediaCollection('photo');
        }

        $user->syncPermissions($this->permissionList);

        if ($isEdit) {
            NotificationsService::fire('user_updated', new UserUpdatedNotification($user));
        } else {
            NotificationsService::fire('user_created', new UserCreatedNotification($user));
        }

        $this->toast()
            ->success(__('platform::app.user_saved'), $isEdit
                ? __('platform::app.user_updated_desc')
                : __('platform::app.user_created_desc')
            )
            ->flash()
            ->send();

        $this->redirect(route('access.users.index'), navigate: true);
    }

    /**
     * Los permisos que el actor puede delegar: solo los que ya tiene.
     *
     * `exists:permissions,name` comprueba que el permiso exista, no que quien
     * lo concede pueda concederlo. Sin esta lista, cualquiera que alcance el
     * formulario se firma a sí mismo el permiso que le falte.
     *
     * @return list<string>
     */
    private function grantablePermissions(): array
    {
        /** @var User $actor */
        $actor = auth()->user();

        return array_values($actor->getAllPermissions()->pluck('name')->all());
    }

    /**
     * Un rol es delegable cuando el actor tiene todos sus permisos. Asignarlo
     * concede el paquete entero, así que la regla es la misma de arriba.
     *
     * @return list<string>
     */
    private function grantableRoles(): array
    {
        $mine = $this->grantablePermissions();

        // `filter` deja huecos en las claves, así que `all()` no produce una
        // list; `values()` los cierra antes de salir.
        return array_values(Role::with('permissions')->get()
            ->filter(fn (Role $role): bool => $role->permissions->pluck('name')->diff($mine)->isEmpty())
            ->pluck('name')
            ->all());
    }

    public function render(): Factory|View
    {
        return view('access::users._form', [
            'roles' => Role::orderBy('display_name')->get()->map(fn ($role): array => [
                'label' => $role->display_name ?? ucfirst((string) $role->name),
                'value' => $role->name,
            ])->toArray(),
        ]);
    }
}
