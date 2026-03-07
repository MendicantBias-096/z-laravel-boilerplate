<?php

namespace App\Notifications;

class UserDeletedNotification extends BaseNotification
{
    protected ?string $event = 'user_deleted';

    public function __construct(
        private string $userName,
    ) {}

    public function toDatabase(object $notifiable): array
    {
        return [
            'title'   => __('notifications.events.user_deleted_title'),
            'message' => __('notifications.events.user_deleted_message', [
                'name' => $this->userName,
            ]),
            'url'     => route('personal.usuarios.index'),
            'type'    => 'user.deleted',
        ];
    }
}
