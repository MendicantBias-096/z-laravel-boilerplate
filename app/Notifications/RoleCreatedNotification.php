<?php

namespace App\Notifications;

class RoleCreatedNotification extends BaseNotification
{
    protected ?string $event = 'role_created';

    public function __construct(
        private string $roleName,
    ) {}

    public function toDatabase(object $notifiable): array
    {
        return [
            'title'   => __('notifications.events.role_created_title'),
            'message' => __('notifications.events.role_created_message', [
                'name' => $this->roleName,
            ]),
            'url'     => route('personal.roles.index'),
            'type'    => 'role.created',
        ];
    }
}
