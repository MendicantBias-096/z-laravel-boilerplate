<?php

declare(strict_types=1);

namespace App\Modules\Access\Livewire\Auth;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class TwoFactorChallenge extends Component
{
    public string $code = '';

    public string $recovery_code = '';

    public bool $showRecovery = false;

    public function render(): Factory|View
    {
        return view('access::auth._two-factor-challenge');
    }
}
