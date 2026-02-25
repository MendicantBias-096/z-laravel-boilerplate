<?php

namespace App\Livewire\Auth;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Register extends Component
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email|max:255|unique:users,email')]
    public string $email = '';

    #[Validate('required|string|min:8|confirmed')]
    public string $password = '';

    #[Validate('required|string')]
    public string $password_confirmation = '';

    public function register(CreateNewUser $creator): void
    {
        $this->validate();

        $user = $creator->create([
            'name'                  => $this->name,
            'email'                 => $this->email,
            'password'              => $this->password,
            'password_confirmation' => $this->password_confirmation,
        ]);

        sleep(3);
        Auth::login($user);

        $this->redirectRoute('dashboard', navigate: true);
    }

    public function render()
    {
        return view('auth._register');
    }
}
