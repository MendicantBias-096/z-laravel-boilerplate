<?php

namespace App\Notifications;

class TestNotification extends BaseNotification
{
    public function __construct(
        private string $title = 'Notificación de prueba',
        private string $message = 'Esta es una notificación de prueba del sistema.',
    ) {}

    public function toDatabase(object $notifiable): array
    {
        return [
            'title'   => $this->title,
            'message' => $this->message,
            'url'     => '/dashboard',
            'type'    => 'test',
        ];
    }
}
