<?php

namespace App\Modules\Access\Livewire\Forms;

use App\Modules\Access\Models\User;
use Livewire\Attributes\Locked;
use Livewire\Form;

class UserForm extends Form
{
    /**
     * A quién se está editando. Lo fija `mount()` y nadie más.
     *
     * Livewire hidrata toda propiedad publica con lo que mande el navegador, y
     * `store()` la usa como clave de `updateOrCreate`: sin el candado, quien
     * puede abrir el alta manda el id de otro usuario y le sobrescribe correo
     * y contraseña. `#[Locked]` no estorba a `fill()`, que corre en el
     * servidor.
     */
    #[Locked]
    public ?int $id = null;

    public string $username = '';

    public string $email = '';

    public string $first_name = '';

    public string $last_name = '';

    public string $password = '';

    public string $password_confirmation = '';

    public ?string $role = null;

    public bool $is_active = true;

    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:users,username,'.($this->id ?? 'NULL')],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.($this->id ?? 'NULL')],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'password' => $this->id ? ['nullable', 'string', 'min:8', 'confirmed'] : ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['nullable', 'string'],
            'role' => ['nullable', 'string', 'exists:roles,name'],
            'is_active' => ['boolean'],
        ];
    }

    public function store(): User
    {
        $data = [
            'username' => $this->username,
            'email' => $this->email,
            'is_active' => $this->is_active,
        ];

        if ($this->password !== '' && $this->password !== '0') {
            $data['password'] = $this->password;
        }

        $user = User::updateOrCreate(['id' => $this->id], $data);

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            ['first_name' => $this->first_name, 'last_name' => $this->last_name]
        );

        $user->syncRoles($this->role ? [$this->role] : []);

        return $user;
    }
}
