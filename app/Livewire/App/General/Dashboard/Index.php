<?php

namespace App\Livewire\App\General\Dashboard;

use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Index extends Component
{
    use Interactions;

    public function render()
    {
        return view('app.general.dashboard._index');
    }
}
