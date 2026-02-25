<?php

namespace App\Livewire;

use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Dashboard extends Component
{
    use Interactions;

    public function render()
    {
        return view('dashboard.index')
            ->layout('components.layouts.app');
    }
}
