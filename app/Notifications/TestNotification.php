<?php

declare(strict_types=1);

namespace App\Notifications;

class TestNotification extends BaseNotification
{
    public function __construct(
        private readonly string $title = 'Notificación de prueba',
        private readonly string $message = 'Esta es una notificación de prueba del sistema.',
    ) {}

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'url' => '/dashboard',
            'type' => 'test',
        ];
    }
}
