<?php

namespace App\Livewire\App\Personal\User;

use App\Livewire\Forms\UserForm;
use App\Models\User;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use TallStackUi\Traits\Interactions;

class Form extends Component
{
    use Interactions;

    public ?User $record = null;

    public UserForm $form;

    public function mount(): void
    {
        if ($this->record) {
            $this->form->fill([
                'id'         => $this->record->id,
                'username'   => $this->record->username,
                'email'      => $this->record->email,
                'first_name' => $this->record->profile?->first_name ?? '',
                'last_name'  => $this->record->profile?->last_name ?? '',
                'role'       => $this->record->roles->first()?->name,
            ]);
        }
    }

    public function save(): void
    {
        $this->form->validate();

        $isEdit = $this->form->id !== null;

        $this->form->store();

        $this->toast()
            ->success('Éxito', $isEdit
                ? 'Usuario actualizado correctamente.'
                : 'Usuario creado correctamente.'
            )
            ->flash()
            ->send();

        $this->redirect(route('personal.usuarios.index'), navigate: true);
    }

    public function render()
    {
        return view('app.personal.users._form', [
            'roles' => Role::orderBy('name')->pluck('name', 'name')->toArray(),
        ]);
    }
}
