<?php

declare(strict_types=1);

namespace App\Modules\Access\Notifications;

use App\Modules\Platform\Notifications\BaseNotification;

class UserDeletedNotification extends BaseNotification
{
    protected ?string $event = 'user_deleted';

    public function __construct(
        private readonly string $userName,
    ) {}

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => __('platform::notifications.events.user_deleted_title'),
            'message' => __('platform::notifications.events.user_deleted_message', [
                'name' => $this->userName,
            ]),
            'url' => route('access.users.index'),
            'type' => 'user.deleted',
        ];
    }
}
