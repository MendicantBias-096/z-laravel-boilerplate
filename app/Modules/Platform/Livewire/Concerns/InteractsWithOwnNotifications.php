<?php

declare(strict_types=1);

namespace App\Modules\Platform\Livewire\Concerns;

use App\Modules\Platform\Models\DatabaseNotification;
use Illuminate\Database\Eloquent\Builder;

/**
 * Las notificaciones del actor, consultadas sobre la tabla de Platform.
 *
 * Antes se leían con `auth()->user()->notifications()`, lo que ataba Platform
 * al modelo de Access y volvía falsa la base del grafo (R9). La dependencia no
 * la veía PHPat porque no había ningún import: viajaba dentro de la relación.
 *
 * Consultar la tabla propia por el id del actor es además lo que R29 pide —
 * cada módulo lee lo suyo— y de paso resuelve el nullable: sin sesión,
 * `auth()->id()` es null, la consulta no devuelve filas y nadie revienta.
 *
 * Vive bajo `Livewire/` porque R18 solo permite `auth()` en la capa de UI.
 */
trait InteractsWithOwnNotifications
{
    /** @return Builder<DatabaseNotification> */
    protected function ownNotifications(): Builder
    {
        // Sin filtrar por `notifiable_type`: hoy el usuario es el único
        // destinatario, y el id ya es único entre notificaciones.
        return DatabaseNotification::query()->where('notifiable_id', auth()->id());
    }

    /** @return Builder<DatabaseNotification> */
    protected function ownUnreadNotifications(): Builder
    {
        return $this->ownNotifications()->whereNull('read_at');
    }
}
