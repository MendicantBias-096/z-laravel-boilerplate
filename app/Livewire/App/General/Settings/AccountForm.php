<?php

namespace App\Livewire\App\General\Settings;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class AccountForm extends Component
{
    use Interactions;

    public string $username = '';

    public string $email = '';

    public function mount(): void
    {
        $user = auth()->user();

        $this->username = $user->username;
        $this->email = $user->email;
    }

    public function save(): void
    {
        $userId = auth()->id();
        $user = auth()->user();

        $this->validate([
            'username' => ['required', 'string', 'max:255', 'alpha_dash', "unique:users,username,{$userId}"],
            'email' => ['required', 'email', 'max:255', "unique:users,email,{$userId}"],
        ]);

        $emailChanged = $user->email !== $this->email;

        $user->update([
            'username' => $this->username,
            'email' => $this->email,
            'email_verified_at' => $emailChanged ? null : $user->email_verified_at,
        ]);

        if ($emailChanged) {
            $user->sendEmailVerificationNotification();
        }

        $this->dispatch('profile-updated');

        $this->toast()->success(__('settings.account_updated'), __('settings.account_saved'))->send();
    }

    public function render(): Factory|View
    {
        return view('app.general.settings._account-form');
    }
}
