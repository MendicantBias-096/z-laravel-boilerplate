<?php

namespace App\Livewire;

use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Dashboard extends Component
{
    use Interactions;

    public function render()
    {
        return view('livewire.dashboard')
            ->layout('components.layouts.app');
    }
}
