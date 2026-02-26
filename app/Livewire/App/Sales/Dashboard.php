<?php

namespace App\Livewire\App\Sales;

use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Dashboard extends Component
{
    use Interactions;

    public function render()
    {
        return view('app.sales.dashboard.index')
            ->layout('components.layouts.app');
    }
}
