<?php

namespace App\Livewire\App\General\Settings;

use Livewire\Component;
use TallStackUi\Traits\Interactions;

class AccountForm extends Component
{
    use Interactions;

    public string $username = '';
    public string $email    = '';

    public function mount(): void
    {
        $user = auth()->user();

        $this->username = $user->username;
        $this->email    = $user->email;
    }

    public function save(): void
    {
        $userId = auth()->id();

        $this->validate([
            'username' => ['required', 'string', 'max:255', 'alpha_dash', "unique:users,username,{$userId}"],
            'email'    => ['required', 'email', 'max:255', "unique:users,email,{$userId}"],
        ]);

        auth()->user()->update([
            'username' => $this->username,
            'email'    => $this->email,
        ]);

        $this->dispatch('profile-updated');

        $this->toast()->success('Cuenta actualizada', 'Tu usuario y correo han sido guardados.')->send();
    }

    public function render()
    {
        return view('app.general.settings._account-form');
    }
}
