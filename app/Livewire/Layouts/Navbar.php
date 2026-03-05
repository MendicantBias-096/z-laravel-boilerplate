<?php

namespace App\Livewire\Layouts;

use Livewire\Attributes\On;
use Livewire\Component;

class Navbar extends Component
{
    #[On('profile-updated')]
    public function refreshUser(): void
    {
        // Re-renders the component with fresh auth data
    }

    public function render()
    {
        return view('components.layouts.navbar', [
            'user' => auth()->user()->load('profile'),
        ]);
    }
}
