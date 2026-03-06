<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class VerifyEmail extends Component
{
    use Interactions;

    public bool $sent = false;

    public function resend(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirect(route('dashboard'), navigate: true);
            return;
        }

        $user->sendEmailVerificationNotification();
        $this->sent = true;
    }

    public function render()
    {
        return view('auth._verify-email');
    }
}
