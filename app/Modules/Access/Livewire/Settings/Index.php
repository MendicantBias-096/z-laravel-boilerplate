<?php

declare(strict_types=1);

namespace App\Modules\Access\Livewire\Settings;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Index extends Component
{
    use Interactions;

    public function render(): Factory|View
    {
        $system = [
            ['label' => 'Versión',  'value' => 'v'.config('app.version', '1.0.0')],
            ['label' => 'Laravel',  'value' => app()->version()],
            ['label' => 'PHP',      'value' => PHP_VERSION],
        ];

        return view('access::settings._index', ['system' => $system]);
    }
}
