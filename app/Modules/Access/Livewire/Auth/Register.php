<?php

declare(strict_types=1);

namespace App\Modules\Access\Livewire\Auth;

use App\Modules\Access\Actions\Fortify\CreateNewUser;
use App\Modules\Access\Livewire\Concerns\InteractsWithRateLimiting;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Register extends Component
{
    use InteractsWithRateLimiting;

    #[Validate('required|string|max:255')]
    public string $first_name = '';

    #[Validate('required|string|max:255')]
    public string $last_name = '';

    #[Validate('required|string|max:255|unique:users,username')]
    public string $username = '';

    #[Validate('required|email|max:255|unique:users,email')]
    public string $email = '';

    #[Validate('required|string|min:8|confirmed')]
    public string $password = '';

    #[Validate('required|string')]
    public string $password_confirmation = '';

    public function register(CreateNewUser $creator): void
    {
        $this->validate();

        // Solo por IP: con el correo en la clave, cambiarlo en cada intento
        // esquiva el límite, y cambiarlo es obligatorio de todos modos porque
        // `unique:users,email` rechaza el repetido. Sin esto, el alta pública
        // es un endpoint que crea filas y manda correos de verificación tan
        // rápido como se le pida.
        $seconds = $this->rateLimitExceeded('register', null, 5);

        if ($seconds !== null) {
            $this->addError('email', __('access::auth.throttle', ['seconds' => $seconds]));

            return;
        }

        $user = $creator->create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
        ]);

        event(new Registered($user));

        Auth::login($user);

        if (! $user->hasVerifiedEmail()) {
            $this->redirectRoute('verification.notice', navigate: true);
        } else {
            $this->redirectRoute('dashboard', navigate: true);
        }
    }

    public function render(): Factory|View
    {
        return view('access::auth._register');
    }
}
