<?php

namespace App\Livewire\App\General\Dashboard;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Index extends Component
{
    use Interactions;

    public function mount(): void
    {
        if ($toast = session('toast')) {
            $this->toast()
                ->{$toast['type']}($toast['title'], $toast['message'])
                ->send();
        }
    }

    public function render(): Factory|View
    {
        return view('app.general.dashboard._index');
    }
}
