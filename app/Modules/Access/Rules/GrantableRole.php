<?php

declare(strict_types=1);

namespace App\Modules\Access\Rules;

use App\Modules\Access\Models\Role;
use App\Modules\Access\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Otorgar un rol es otorgar sus permisos, así que se juzga por ellos.
 *
 * Un rol no es una categoría con rango: es un paquete de permisos y un atajo
 * para no repartirlos de uno en uno. De ahí que la regla no compare roles
 * entre sí —no hay jerarquía que comparar— sino que mire dentro: si el paquete
 * contiene algo que el actor no tiene, no puede entregarlo.
 *
 * Y como los roles se crean desde la interfaz, esto los cubre según nacen. Un
 * «supervisor» inventado esta tarde se valida igual que «admin», sin tocar
 * configuración: la respuesta sale de sus permisos, no de una lista escrita a
 * mano que alguien tendría que acordarse de actualizar.
 *
 * Que el rol exista lo comprueba la regla que va antes.
 *
 * El actor llega por el constructor y no de `auth()` (R18).
 */
class GrantableRole implements ValidationRule
{
    public function __construct(private readonly ?User $actor) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || $value === '') {
            return;
        }

        $role = Role::where('name', $value)->with('permissions')->first();

        if (! $role instanceof Role) {
            return;
        }

        $fueraDeAlcance = $role->permissions
            ->pluck('name')
            ->reject(fn (string $permission): bool => $this->actor?->can($permission) === true);

        if ($fueraDeAlcance->isNotEmpty()) {
            $fail('validation.grant_role_beyond_own')->translate([
                'role' => $role->display_name ?? $role->name,
                'count' => $fueraDeAlcance->count(),
            ]);
        }
    }
}
