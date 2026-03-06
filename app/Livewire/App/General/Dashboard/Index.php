<?php

namespace App\Livewire\App\General\Dashboard;

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

    public function render()
    {
        return view('app.general.dashboard._index');
    }
}
