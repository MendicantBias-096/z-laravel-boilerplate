<?php

declare(strict_types=1);

namespace App\Modules\Platform\Contracts;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

/**
 * Quién debe recibir una notificación, sin que Platform sepa qué es un usuario.
 *
 * Platform es la base del grafo (R9) y no puede depender de `Access`. Pero
 * necesita resolver destinatarios, y quién tiene un permiso es negocio de
 * `Access`. La dependencia se invierte: Platform declara qué necesita, `Access`
 * lo implementa y lo registra en el contenedor.
 *
 * Cicatriz: `NotificationsService` hacía `User::permission($p)->get()` — la
 * base preguntándole a la capa de encima. Son veinticinco líneas, y son el
 * ejemplo canónico que cada módulo de negocio va a copiar.
 */
interface NotificationAudience
{
    /**
     * Los destinatarios que tienen el permiso dado.
     *
     * @return Collection<int, Notifiable>
     */
    public function withPermission(string $permission): Collection;

    /**
     * Los destinatarios que tienen el rol dado.
     *
     * @return Collection<int, Notifiable>
     */
    public function withRole(string $role): Collection;
}
