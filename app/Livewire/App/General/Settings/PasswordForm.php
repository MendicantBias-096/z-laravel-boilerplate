<?php

namespace App\Livewire\App\General\Settings;

use Livewire\Component;
use TallStackUi\Traits\Interactions;

class PasswordForm extends Component
{
    use Interactions;

    public string $current_password      = '';
    public string $password              = '';
    public string $password_confirmation = '';

    public function save(): void
    {
        $this->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        auth()->user()->update(['password' => $this->password]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->toast()->success('Contraseña actualizada', 'Tu contraseña ha sido cambiada correctamente.')->send();
    }

    public function render()
    {
        return view('app.general.settings._password-form');
    }
}
