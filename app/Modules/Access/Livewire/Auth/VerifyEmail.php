<?php

namespace App\Modules\Access\Livewire\Auth;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
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

    public function render(): Factory|View
    {
        return view('access::auth._verify-email');
    }
}
