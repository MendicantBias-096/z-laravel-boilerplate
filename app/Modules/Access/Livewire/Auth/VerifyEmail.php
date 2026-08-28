<?php

namespace App\Modules\Access\Livewire\Auth;

use App\Modules\Access\Livewire\Concerns\InteractsWithCurrentUser;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class VerifyEmail extends Component
{
    use Interactions, InteractsWithCurrentUser;

    public bool $sent = false;

    public function resend(): void
    {
        $user = $this->currentUser();

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
