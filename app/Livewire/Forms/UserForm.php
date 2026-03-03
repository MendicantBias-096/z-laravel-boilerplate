<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Livewire\Form;

class UserForm extends Form
{
    public ?int $id = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public ?string $role = null;

    public function rules(): array
    {
        return [
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'max:255', 'unique:users,email,' . ($this->id ?? 'NULL')],
            'password'              => $this->id ? ['nullable', 'string', 'min:8', 'confirmed'] : ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['nullable', 'string'],
            'role'                  => ['required', 'string', 'exists:roles,name'],
        ];
    }

    public function store(): User
    {
        $data = [
            'name'  => $this->name,
            'email' => $this->email,
        ];

        if ($this->password) {
            $data['password'] = $this->password;
        }

        $user = User::updateOrCreate(['id' => $this->id], $data);

        $user->syncRoles([$this->role]);

        return $user;
    }
}
