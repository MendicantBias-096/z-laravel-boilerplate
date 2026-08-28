<?php

declare(strict_types=1);

namespace App\Modules\Access\Notifications;

use App\Modules\Platform\Notifications\BaseNotification;

class RoleCreatedNotification extends BaseNotification
{
    protected ?string $event = 'role_created';

    public function __construct(
        private readonly string $roleName,
    ) {}

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => __('platform::notifications.events.role_created_title'),
            'message' => __('platform::notifications.events.role_created_message', [
                'name' => $this->roleName,
            ]),
            'url' => route('access.roles.index'),
            'type' => 'role.created',
        ];
    }
}
