<?php

declare(strict_types=1);

namespace App\Modules\Access\Policies;

use App\Modules\Access\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Las dos guardas que vivían como `abort_if` en la ruta.
 *
 * Estaban ahí porque `Gate::before` devolvía true para el rol admin y cortaba
 * antes de cualquier Policy, así que una Policy no habría corrido justo para
 * el actor al que estas dos reglas atan. Quitado el atajo, vuelven a su sitio
 * (R39) y pasan a ser reutilizables desde el componente y testeables sin HTTP.
 *
 * Se descubre sola: `Gate::guessPolicyName()` sustituye `\Models\` por
 * `\Policies\`, que es la única pieza de un módulo que Laravel sigue
 * encontrando por convención.
 */
class UserPolicy
{
    /**
     * Editar a otro usuario desde la pantalla de administración.
     *
     * El propio perfil se edita en `/settings`, que es otra pantalla con otras
     * reglas: aquí es un 403, no un descuido.
     */
    public function update(User $actor, User $user): Response
    {
        if ($actor->id === $user->id) {
            return Response::deny(__('platform::app.no_self_edit'));
        }

        // 404 y no 403: existir es justo lo que un usuario protegido no
        // reconoce ante quien no debería alcanzarlo.
        if ($user->is_protected) {
            return Response::denyAsNotFound();
        }

        return Response::allow();
    }
}
