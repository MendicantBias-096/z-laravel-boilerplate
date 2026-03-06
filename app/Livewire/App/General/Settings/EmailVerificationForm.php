<?php

namespace App\Livewire\App\General\Settings;

use Livewire\Component;
use TallStackUi\Traits\Interactions;

class EmailVerificationForm extends Component
{
    use Interactions;

    public bool $sent = false;

    public function resend(): void
    {
        $user = auth()->user();

        if ($user->hasVerifiedEmail()) {
            return;
        }

        $user->sendEmailVerificationNotification();
        $this->sent = true;

        $this->toast()->success(__('settings.verification_sent'), __('settings.verification_sent_desc'))->send();
    }

    public function render()
    {
        return view('app.general.settings._email-verification-form');
    }
}
