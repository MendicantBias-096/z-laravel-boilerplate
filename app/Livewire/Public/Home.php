<?php

namespace App\Livewire\Public;

use Livewire\Component;

class Home extends Component
{
    public function render()
    {
        return view('public.home.index')
            ->layout('components.layouts.public', [
                'title' => config('app.name') . ' — Plataforma de gestión',
            ]);
    }
}
