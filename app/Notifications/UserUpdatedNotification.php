<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;

class UserUpdatedNotification extends BaseNotification
{
    protected ?string $event = 'user_updated';

    public function __construct(
        private readonly User $updatedUser,
    ) {}

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => __('notifications.events.user_updated_title'),
            'message' => __('notifications.events.user_updated_message', [
                'name' => $this->updatedUser->name,
            ]),
            'url' => route('personal.usuarios.edit', $this->updatedUser),
            'type' => 'user.updated',
        ];
    }
}
