<?php

namespace App\Livewire\App\General\Settings;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class PasswordForm extends Component
{
    use Interactions;

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function save(): void
    {
        $this->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        auth()->user()->update(['password' => $this->password]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->toast()->success(__('settings.password_updated'), __('settings.password_saved'))->send();
    }

    public function render(): Factory|View
    {
        return view('app.general.settings._password-form');
    }
}
