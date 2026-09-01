<?php

declare(strict_types=1);

namespace App\Modules\Access\Livewire\Auth;

use App\Modules\Access\Database\Seeders\UsersTableSeeder;
use App\Modules\Access\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Login extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    public function login(): void
    {
        $this->validate();

        $throttleKey = Str::lower($this->email).'|'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('email', "Too many login attempts. Please try again in {$seconds} seconds.");

            return;
        }

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($throttleKey);
            $this->addError('email', 'These credentials do not match our records.');

            return;
        }

        RateLimiter::clear($throttleKey);
        Session::regenerate();

        $this->redirectRoute('dashboard', navigate: true);
    }

    /**
     * Acceso directo sin contraseña para los usuarios sembrados.
     *
     * Solo existe en local. En cualquier otro entorno responde 403 aunque
     * alguien invoque el método desde el cliente, porque la comprobación
     * vive en el servidor y no en el `@if` de la vista.
     *
     * La segunda guarda es la que importa: `isLocal()` depende de una variable
     * de entorno, y una plantilla copiada con `APP_ENV=local` bastaría para
     * entrar en cualquier cuenta con solo su correo. Acotado a las cuentas de
     * ejemplo, ese fallo deja de dar acceso a nada que exista en producción.
     */
    public function quickLogin(string $email): void
    {
        abort_unless(app()->isLocal(), 403);
        abort_unless(in_array($email, UsersTableSeeder::DEMO_EMAILS, true), 403);

        $user = User::where('email', $email)->first();

        // Sin seeders la tabla está vacía y `firstOrFail()` daba un 404 mudo
        // que no decía qué faltaba. El error nombra el arreglo.
        if ($user === null) {
            $this->addError('email', "No user {$email}. Run: php artisan db:seed");

            return;
        }

        Auth::login($user);
        Session::regenerate();

        $this->redirectRoute('dashboard', navigate: true);
    }

    public function render(): Factory|View
    {
        return view('access::auth._login');
    }
}
