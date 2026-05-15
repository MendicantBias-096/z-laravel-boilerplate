<?php

declare(strict_types=1);

namespace App\Notifications;

class RoleDeletedNotification extends BaseNotification
{
    protected ?string $event = 'role_deleted';

    public function __construct(
        private readonly string $roleName,
    ) {}

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => __('notifications.events.role_deleted_title'),
            'message' => __('notifications.events.role_deleted_message', [
                'name' => $this->roleName,
            ]),
            'url' => route('personal.roles.index'),
            'type' => 'role.deleted',
        ];
    }
}
