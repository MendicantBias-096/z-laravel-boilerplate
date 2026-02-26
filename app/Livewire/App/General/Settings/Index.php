<?php

namespace App\Livewire\App\General\Settings;

use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Index extends Component
{
    use Interactions;

    public function render()
    {
        return view('app.general.settings._index');
    }
}
