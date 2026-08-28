<?php

declare(strict_types=1);

namespace App\Modules\Access\Livewire\Concerns;

use App\Modules\Access\Models\User;

/**
 * El usuario de la sesión, tipado y sin null.
 *
 * `auth()->user()` devuelve `User|null` porque el contenedor no sabe qué
 * middleware protege la ruta. En un componente que solo se alcanza tras
 * `auth` el null no ocurre, y afirmarlo una vez aquí evita repetir la
 * comprobación en cada método —que es lo que PHPStan pedía en 24 sitios—.
 *
 * La invariante está verificada, no supuesta: las páginas públicas usan
 * `x-layouts.public`, y toda ruta que renderiza `x-layouts.app` lleva
 * middleware `auth`. Si eso deja de ser cierto, el `assert` lo dice en
 * desarrollo en vez de dejar pasar un null.
 *
 * Vive bajo `Livewire/` y no en `Traits/` porque R18 solo permite `auth()` en
 * la capa de UI: un Action recibe el actor como parámetro, y en un job no hay
 * sesión que consultar.
 */
trait InteractsWithCurrentUser
{
    protected function currentUser(): User
    {
        $user = auth()->user();

        assert($user instanceof User);

        return $user;
    }
}
