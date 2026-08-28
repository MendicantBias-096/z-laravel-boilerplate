<?php

declare(strict_types=1);

namespace App\Modules\Access\Notifications;

use App\Modules\Access\Models\User;
use App\Modules\Platform\Notifications\BaseNotification;

class UserCreatedNotification extends BaseNotification
{
    protected ?string $event = 'user_created';

    public function __construct(
        private readonly User $createdUser,
    ) {}

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => __('platform::notifications.events.user_created_title'),
            'message' => __('platform::notifications.events.user_created_message', [
                'name' => $this->createdUser->name,
            ]),
            'url' => route('access.users.index'),
            'type' => 'user.created',
        ];
    }
}
