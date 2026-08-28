<?php

namespace App\Modules\Access\Livewire\Auth;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ForgotPassword extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    public bool $linkSent = false;

    public function sendResetLink(): void
    {
        $this->validate();

        Password::sendResetLink(['email' => $this->email]);

        // Siempre el mismo desenlace, exista el correo o no. Mostrar «no
        // encontramos ningún usuario con ese correo» convierte esta pantalla
        // en un oráculo: se prueba una lista y se sabe quién tiene cuenta.
        // También se calla el throttle, que filtra lo mismo por otra vía.
        $this->linkSent = true;
    }

    public function render(): Factory|View
    {
        return view('access::auth._forgot-password');
    }
}
