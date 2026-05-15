<?php

namespace App\Livewire\Layouts;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class Navbar extends Component
{
    #[On('profile-updated')]
    public function refreshUser(): void
    {
        // Re-renders the component with fresh auth data
    }

    public function render(): Factory|View
    {
        return view('components.layouts.navbar', [
            'user' => auth()->user()->load('profile'),
        ]);
    }
}
