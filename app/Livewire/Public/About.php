<?php

namespace App\Livewire\Public;

use Livewire\Component;

class About extends Component
{
    public function render()
    {
        return view('public.about.index')
            ->layout('components.layouts.public', [
                'title' => 'Nosotros — ' . config('app.name'),
            ]);
    }
}
