<?php

declare(strict_types=1);

namespace App\Modules\Access\Notifications;

use App\Modules\Access\Models\User;
use App\Modules\Platform\Contracts\NotificationAudience;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

/**
 * La implementación de `NotificationAudience`: en este producto, quien recibe
 * una notificación es un usuario.
 *
 * Vive en `Access` porque «quién tiene este permiso» es su negocio, y se
 * registra en `AccessServiceProvider`. Platform solo conoce la interfaz.
 */
final class UserAudience implements NotificationAudience
{
    /** @return Collection<int, Notifiable> */
    public function withPermission(string $permission): Collection
    {
        return User::permission($permission)->get();
    }

    /** @return Collection<int, Notifiable> */
    public function withRole(string $role): Collection
    {
        return User::role($role)->get();
    }
}
