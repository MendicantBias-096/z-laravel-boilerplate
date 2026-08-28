<?php

namespace App\Modules\Platform\Services;

use App\Modules\Platform\Contracts\NotificationAudience;
use App\Modules\Platform\Events\NewNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class NotificationsService
{
    /**
     * Disparar una notificación basada en un evento del config.
     *
     * Busca el evento en config/notifications.php, resuelve los usuarios
     * que tienen el permiso asociado y les envía la notificación.
     */
    public static function fire(string $event, Notification $notification): void
    {
        $config = config("notifications.events.{$event}");

        if (! $config) {
            Log::warning("NotificationsService: evento [{$event}] no definido en config/notifications.php");

            return;
        }

        $permission = $config['permission'];
        $users = app(NotificationAudience::class)->withPermission($permission);

        if ($users->isEmpty()) {
            Log::info("NotificationsService: sin usuarios con permiso [{$permission}] para evento [{$event}]");

            return;
        }

        NotificationFacade::send($users, $notification);

        // Broadcast en tiempo real a cada usuario
        foreach ($users as $user) {
            $latest = $user->notifications()->latest()->first();
            if ($latest) {
                broadcast(new NewNotification($user->id, [
                    'id' => $latest->id,
                    'title' => $latest->data['title'] ?? '',
                    'message' => $latest->data['message'] ?? '',
                    'url' => $latest->data['url'] ?? null,
                    'created_at' => $latest->created_at->diffForHumans(),
                ]));
            }
        }
    }

    /**
     * Obtener los canales configurados para un evento.
     */
    public static function channelsFor(string $event): array
    {
        return config("notifications.events.{$event}.channels", ['database']);
    }

    /**
     * Enviar notificación a todos los usuarios con un permiso específico.
     */
    public static function notifyUsersWithPermission(string $permission, Notification $notification): void
    {
        $users = app(NotificationAudience::class)->withPermission($permission);

        if ($users->isEmpty()) {
            Log::info("NotificationsService: no users found with permission [{$permission}]");

            return;
        }

        NotificationFacade::send($users, $notification);
    }

    /**
     * Enviar notificación a todos los usuarios con un rol específico.
     */
    public static function sendToRole(string $role, Notification $notification): void
    {
        $users = app(NotificationAudience::class)->withRole($role);

        if ($users->isEmpty()) {
            Log::info("NotificationsService: no users found with role [{$role}]");

            return;
        }

        NotificationFacade::send($users, $notification);
    }

    /**
     * Enviar notificación a usuarios específicos.
     */
    public static function sendToUsers($users, Notification $notification): void
    {
        NotificationFacade::send($users, $notification);
    }
}
