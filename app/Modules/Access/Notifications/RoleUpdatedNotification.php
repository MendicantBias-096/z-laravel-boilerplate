<?php

declare(strict_types=1);

namespace App\Modules\Access\Notifications;

use App\Modules\Platform\Notifications\BaseNotification;

class RoleUpdatedNotification extends BaseNotification
{
    protected ?string $event = 'role_updated';

    public function __construct(
        private readonly string $roleName,
    ) {}

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => __('platform::notifications.events.role_updated_title'),
            'message' => __('platform::notifications.events.role_updated_message', [
                'name' => $this->roleName,
            ]),
            'url' => route('access.roles.index'),
            'type' => 'role.updated',
        ];
    }
}
