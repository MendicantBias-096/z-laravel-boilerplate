<?php

namespace App\Livewire\Auth;

use Livewire\Component;

class TwoFactorChallenge extends Component
{
    public string $code            = '';
    public string $recovery_code   = '';
    public bool   $showRecovery    = false;

    public function render()
    {
        return view('auth._two-factor-challenge');
    }
}
