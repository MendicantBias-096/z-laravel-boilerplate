<?php

namespace App\Livewire\App\Personal\User;

use App\Livewire\Forms\UserForm;
use App\Models\Role;
use App\Models\User;
use App\Notifications\UserCreatedNotification;
use App\Notifications\UserUpdatedNotification;
use App\Services\NotificationsService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use TallStackUi\Traits\Interactions;

class Form extends Component
{
    use Interactions, WithFileUploads;

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

        $this->toast()->info(__('app.user_perms_restored'), __('app.user_perms_restored_desc'))->send();
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
        $this->toast()->info(__('app.user_perms_loaded'), __('app.user_perms_loaded_desc', ['name' => $templateName]))->send();
    }

    public function save(): void
    {
        $this->validate(['photo' => ['nullable', 'image', 'max:2048']]);

        $this->form->validate();

        $this->validate([
            'permissionList' => ['array'],
            'permissionList.*' => ['string', 'exists:permissions,name'],
        ]);

        $isEdit = $this->form->id !== null;

        $user = $this->form->store();

        if ($this->photo) {
            $user->profile->addMedia($this->photo->getRealPath())
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
            ->success(__('app.user_saved'), $isEdit
                ? __('app.user_updated_desc')
                : __('app.user_created_desc')
            )
            ->flash()
            ->send();

        $this->redirect(route('personal.usuarios.index'), navigate: true);
    }

    public function render(): Factory|View
    {
        return view('app.personal.users._form', [
            'roles' => Role::orderBy('display_name')->get()->map(fn ($r): array => [
                'label' => $r->display_name ?? ucfirst((string) $r->name),
                'value' => $r->name,
            ])->toArray(),
        ]);
    }
}
