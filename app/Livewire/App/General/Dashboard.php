<?php

namespace App\Livewire\App\General;

use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Dashboard extends Component
{
    use Interactions;

    public function render()
    {
        return view('app.general.dashboard.index')
            ->layout('components.layouts.app');
    }
}
