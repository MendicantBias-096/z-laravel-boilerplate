<?php

namespace App\Modules\Access\Livewire\Auth;

use App\Modules\Access\Livewire\Concerns\InteractsWithRateLimiting;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ForgotPassword extends Component
{
    use InteractsWithRateLimiting;

    #[Validate('required|email')]
    public string $email = '';

    public bool $linkSent = false;

    public function sendResetLink(): void
    {
        $this->validate();

        // Por IP y no por correo: `auth.passwords.throttle` ya espacia los
        // envíos de un mismo usuario, y lo que falta es frenar a quien recorre
        // una lista de correos desde una máquina.
        //
        // El límite no se anuncia: decir «demasiados intentos» solo cuando el
        // correo existe convierte el límite en el oráculo que el resto de este
        // método evita. Se corta el envío y se responde lo mismo de siempre.
        if ($this->rateLimitExceeded('reset-link', null, 5) === null) {
            Password::sendResetLink(['email' => $this->email]);
        }

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
