<?php

namespace App\Livewire\App\General\Settings;

use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Index extends Component
{
    use Interactions;

    public function render()
    {
        $system = [
            ['label' => 'Versión',  'value' => 'v' . config('app.version', '1.0.0')],
            ['label' => 'Laravel',  'value' => app()->version()],
            ['label' => 'PHP',      'value' => PHP_VERSION],
        ];

        return view('app.general.settings._index', compact('system'));
    }
}
