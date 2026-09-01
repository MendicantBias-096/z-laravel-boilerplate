<?php

namespace App\Modules\Access\Livewire\Users;

use App\Modules\Access\Livewire\Forms\UserForm;
use App\Modules\Access\Models\Role;
use App\Modules\Access\Models\User;
use App\Modules\Access\Notifications\UserCreatedNotification;
use App\Modules\Access\Notifications\UserUpdatedNotification;
use App\Modules\Access\Rules\GrantablePermission;
use App\Modules\Access\Rules\GrantableRole;
use App\Modules\Access\Traits\Livewire\WithPermissionMatrix;
use App\Modules\Platform\Services\NotificationsService;
use App\Modules\Platform\Traits\Livewire\HasFormSections;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use TallStackUi\Traits\Interactions;

class Form extends Component
{
    use HasFormSections;
    use Interactions, WithFileUploads;
    use WithPermissionMatrix;

    #[Locked]
    public ?User $record = null;

    public UserForm $form;

    /** @var TemporaryUploadedFile|null */
    public $photo;

    public array $permissionList = [];

    public array $originalPermissions = [];

    /**
     * Las secciones del formulario, en orden.
     *
     * El badge de accesos cuenta permisos concedidos sobre concedibles: es el
     * dato que un formulario partido esconde, porque los permisos viven en una
     * pestaña que puede no estar abierta.
     *
     * @return list<array{key: string, icon: string, label: string, badge?: string}>
     */
    /**
     * @return list<array{key: string, icon: string, label: string, badge?: string}>
     */
    protected function formSections(): array
    {
        return [
            ['key' => 'identity', 'icon' => 'lucide-user-round', 'label' => __('platform::app.user_section_identity')],
            ['key' => 'account', 'icon' => 'lucide-key-round', 'label' => __('platform::app.user_section_account')],
            [
                'key' => 'access',
                'icon' => 'lucide-shield',
                'label' => __('platform::app.user_section_access'),
                // Sobre lo que el actor puede repartir, no sobre el catálogo
                // entero: un supervisor con diez permisos vería «3/84» y
                // pensaría que le faltan setenta y cuatro por marcar.
                'badge' => count($this->permissionList).'/'.$this->concedibles(),
            ],
        ];
    }

    /** Cuántos permisos puede repartir quien está mirando la ficha. */
    private function concedibles(): int
    {
        return auth()->user()?->getAllPermissions()->count() ?? 0;
    }

    /**
     * @return array<string, string>
     */
    protected function sectionFields(): array
    {
        return [
            'photo' => 'identity',
            'form.first_name' => 'identity',
            'form.last_name' => 'identity',
            'form.username' => 'identity',
            'form.email' => 'account',
            'form.password' => 'account',
            'form.password_confirmation' => 'account',
            'form.is_active' => 'account',
            'form.role' => 'access',
            'permissionList' => 'access',
        ];
    }

    /**
     * Qué secciones tienen cambios sin guardar, para que el rail lo señale y
     * nadie se vaya creyendo que ya guardó lo que no se ve.
     *
     * Se compara contra el registro y no contra una copia de los valores
     * iniciales: una propiedad privada no sobrevive a la siguiente petición de
     * Livewire, así que esa copia estaría vacía justo cuando hace falta.
     *
     * En un alta no hay con qué comparar, y marcar todo como sucio desde el
     * primer carácter convierte el aviso en ruido.
     *
     * @return array<string, bool>
     */
    #[Computed]
    public function dirtySections(): array
    {
        if (! $this->record instanceof User) {
            return [];
        }

        $perfil = $this->record->profile;

        return [
            'identity' => $this->photo !== null
                || $this->form->first_name !== ($perfil->first_name ?? '')
                || $this->form->last_name !== ($perfil->last_name ?? '')
                || $this->form->username !== $this->record->username,
            'account' => $this->form->email !== $this->record->email
                || $this->form->password !== ''
                || $this->form->is_active !== $this->record->is_active,
            'access' => $this->permissionList !== $this->originalPermissions
                || $this->form->role !== $this->record->getRoleNames()->first(),
        ];
    }

    public function mount(): void
    {
        $this->section = $this->section ?: $this->firstSectionKey();

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
        //
        // Vive en `save()` y no en `guardar()` a propósito: éste es el método
        // que el navegador alcanza, y esconder la guarda un nivel más abajo la
        // vuelve invisible para quien lee —y para R58—.
        if ($this->record instanceof User) {
            $this->authorize('access.users.update');
            $this->authorize('update', $this->record);
        } else {
            $this->authorize('access.users.create');
        }

        $this->saveShowingErrors(fn () => $this->guardar());
    }

    private function guardar(): void
    {
        $this->validate(['photo' => ['nullable', 'image', 'max:2048']]);

        $this->form->validate();

        $this->validate([
            'permissionList' => ['array'],
            'permissionList.*' => ['string', new GrantablePermission(auth()->user())],
            'form.role' => ['nullable', 'exists:roles,name', new GrantableRole(auth()->user())],
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
