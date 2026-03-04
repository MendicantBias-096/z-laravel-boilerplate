<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Livewire\Form;

class UserForm extends Form
{
    public ?int $id = null;

    public string $username = '';

    public string $email = '';

    public string $first_name = '';

    public string $last_name = '';

    public string $password = '';

    public string $password_confirmation = '';

    public ?string $role = null;

    public function rules(): array
    {
        return [
            'username'              => ['required', 'string', 'max:255', 'alpha_dash', 'unique:users,username,' . ($this->id ?? 'NULL')],
            'email'                 => ['required', 'email', 'max:255', 'unique:users,email,' . ($this->id ?? 'NULL')],
            'first_name'            => ['required', 'string', 'max:255'],
            'last_name'             => ['required', 'string', 'max:255'],
            'password'              => $this->id ? ['nullable', 'string', 'min:8', 'confirmed'] : ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['nullable', 'string'],
            'role'                  => ['required', 'string', 'exists:roles,name'],
        ];
    }

    public function store(): User
    {
        $data = [
            'username' => $this->username,
            'email'    => $this->email,
        ];

        if ($this->password) {
            $data['password'] = $this->password;
        }

        $user = User::updateOrCreate(['id' => $this->id], $data);

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            ['first_name' => $this->first_name, 'last_name' => $this->last_name]
        );

        $user->syncRoles([$this->role]);

        return $user;
    }
}
