<?php

namespace App\Livewire\App\Operations;

use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Dashboard extends Component
{
    use Interactions;

    public function render()
    {
        return view('app.operations.dashboard.index')
            ->layout('components.layouts.app');
    }
}
