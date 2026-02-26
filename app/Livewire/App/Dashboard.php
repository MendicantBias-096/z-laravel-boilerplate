<?php

namespace App\Livewire\App;

use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Dashboard extends Component
{
    use Interactions;

    public function render()
    {
        return view('app.dashboard.index')
            ->layout('components.layouts.app');
    }
}
