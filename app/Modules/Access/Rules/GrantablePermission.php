<?php

declare(strict_types=1);

namespace App\Modules\Access\Rules;

use App\Modules\Access\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Nadie reparte un permiso que no tiene.
 *
 * Es el techo de lo que alguien puede conceder, y se mide en permisos y no en
 * roles a propósito: los roles nacen desde la interfaz, así que una lista de
 * «quién puede otorgar qué» habría que mantenerla a mano cada vez que aparece
 * uno. Preguntando por el permiso, un rol nuevo queda cubierto solo.
 *
 * `can()` y no los permisos directos: cuentan también los que llegan por el
 * rol, porque si no, alguien cuyo poder viene entero de su rol no podría
 * conceder nada.
 *
 * El administrador la atraviesa por tener todos los permisos, no por llamarse
 * admin: aquí no hay `Gate::before` que le abra paso (R39), y quien lo
 * mantiene cierto es `AdminTienePermisosTest`.
 *
 * El actor llega por el constructor y no de `auth()` (R18): fuera de la capa
 * de UI no hay sesión que consultar —un job, un comando, un seeder—, y así la
 * regla se prueba sin montar una.
 */
class GrantablePermission implements ValidationRule
{
    public function __construct(private readonly ?User $actor) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || $value === '') {
            return;
        }

        if ($this->actor?->can($value) !== true) {
            $fail('validation.grant_beyond_own')->translate(['permission' => $value]);
        }
    }
}
