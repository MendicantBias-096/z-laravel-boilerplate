<?php

namespace App\Modules\Access\Livewire\Settings;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use TallStackUi\Traits\Interactions;
use App\Modules\Access\Livewire\Concerns\InteractsWithCurrentUser;

class PasswordForm extends Component
{
    use InteractsWithCurrentUser, Interactions;

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function save(): void
    {
        $this->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $this->currentUser()->update(['password' => $this->password]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->toast()->success(__('platform::settings.password_updated'), __('platform::settings.password_saved'))->send();
    }

    public function render(): Factory|View
    {
        return view('access::settings._password-form');
    }
}
