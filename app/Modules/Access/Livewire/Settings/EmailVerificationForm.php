<?php

namespace App\Modules\Access\Livewire\Settings;

use App\Modules\Access\Livewire\Concerns\InteractsWithCurrentUser;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class EmailVerificationForm extends Component
{
    use Interactions, InteractsWithCurrentUser;

    public bool $sent = false;

    public function resend(): void
    {
        $user = $this->currentUser();

        if ($user->hasVerifiedEmail()) {
            return;
        }

        $user->sendEmailVerificationNotification();
        $this->sent = true;

        $this->toast()->success(__('platform::settings.verification_sent'), __('platform::settings.verification_sent_desc'))->send();
    }

    public function render(): Factory|View
    {
        return view('access::settings._email-verification-form');
    }
}
