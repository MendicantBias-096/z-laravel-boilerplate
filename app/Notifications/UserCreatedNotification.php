<?php

namespace App\Notifications;

use App\Models\User;

class UserCreatedNotification extends BaseNotification
{
    protected ?string $event = 'user_created';

    public function __construct(
        private User $createdUser,
    ) {}

    public function toDatabase(object $notifiable): array
    {
        return [
            'title'   => __('notifications.events.user_created_title'),
            'message' => __('notifications.events.user_created_message', [
                'name' => $this->createdUser->name,
            ]),
            'url'     => route('personal.usuarios.index'),
            'type'    => 'user.created',
        ];
    }
}
