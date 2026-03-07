<?php

namespace App\Notifications;

use App\Services\NotificationsService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

abstract class BaseNotification extends Notification
{
    use Queueable;

    /**
     * Evento del config (config/notifications.php).
     * Si se define, los canales se resuelven automáticamente desde el config.
     */
    protected ?string $event = null;

    public function via(object $notifiable): array
    {
        if ($this->event) {
            return NotificationsService::channelsFor($this->event);
        }

        return ['database'];
    }

    abstract public function toDatabase(object $notifiable): array;
}
