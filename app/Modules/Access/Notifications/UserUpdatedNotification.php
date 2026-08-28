<?php

declare(strict_types=1);

namespace App\Modules\Access\Notifications;

use App\Modules\Access\Models\User;
use App\Modules\Platform\Notifications\BaseNotification;

class UserUpdatedNotification extends BaseNotification
{
    protected ?string $event = 'user_updated';

    public function __construct(
        private readonly User $updatedUser,
    ) {}

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => __('platform::notifications.events.user_updated_title'),
            'message' => __('platform::notifications.events.user_updated_message', [
                'name' => $this->updatedUser->name,
            ]),
            'url' => route('access.users.edit', $this->updatedUser),
            'type' => 'user.updated',
        ];
    }
}
