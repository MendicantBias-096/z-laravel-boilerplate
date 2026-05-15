<?php

declare(strict_types=1);

namespace App\Livewire\Public\About;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Index extends Component
{
    public function render(): Factory|View
    {
        return view('public.about._index');
    }
}
