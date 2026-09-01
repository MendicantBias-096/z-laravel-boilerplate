<?php

declare(strict_types=1);

namespace App\Modules\Access\Http\Resources;

use App\Modules\Access\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * La forma pública de un usuario.
 *
 * `/api/user` devolvía el modelo entero. `$hidden` tapaba la contraseña y los
 * secretos de 2FA, pero seguía publicando `is_active`, `is_protected` y
 * `deleted_at`: quién está desactivado, quién está protegido y quién fue
 * borrado, que es exactamente el mapa que se dibuja antes de atacar.
 *
 * Un Resource invierte la regla: se publica lo que se nombra aquí, así que una
 * columna nueva no aparece en la API por el mero hecho de existir.
 *
 * @mixin User
 */
class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'first_name' => $this->profile?->first_name,
            'last_name' => $this->profile?->last_name,
            'roles' => $this->getRoleNames(),
            'created_at' => $this->created_at,
        ];
    }
}
