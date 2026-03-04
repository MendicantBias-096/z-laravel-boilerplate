<?php

namespace App\Livewire\App\General\Settings;

use Livewire\Component;
use TallStackUi\Traits\Interactions;

class ProfileForm extends Component
{
    use Interactions;

    public string $first_name = '';
    public string $last_name  = '';

    public function mount(): void
    {
        $profile = auth()->user()->profile;

        $this->first_name = $profile?->first_name ?? '';
        $this->last_name  = $profile?->last_name ?? '';
    }

    public function save(): void
    {
        $this->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
        ]);

        auth()->user()->profile()->updateOrCreate(
            ['user_id' => auth()->id()],
            ['first_name' => $this->first_name, 'last_name' => $this->last_name]
        );

        $this->toast()->success('Perfil actualizado', 'Tu información personal ha sido guardada.')->send();
    }

    public function render()
    {
        return view('app.general.settings._profile-form');
    }
}
