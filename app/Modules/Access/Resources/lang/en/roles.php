<?php

return [

    'title' => 'Roles',
    'create' => 'New role',
    'users' => 'Users',
    'new_user' => 'New user',

    'groups' => [
        'access' => 'Access',
        'configuracion' => 'Configuration',
        'notificaciones' => 'Notifications',
        'other' => 'Other',
    ],

    'modules' => [
        'usuarios' => 'Users',
        'roles' => 'Roles & permissions',
        'sistema' => 'System',
        'notif_usuarios' => 'Users',
        'notif_roles' => 'Roles',
    ],

    'permissions' => [
        'access.users.view' => 'View',
        'access.users.create' => 'Create',
        'access.users.update' => 'Edit',
        'access.users.delete' => 'Delete',
        'access.users.restore' => 'Restore',

        'access.roles.view' => 'View',
        'access.roles.create' => 'Create',
        'access.roles.update' => 'Edit',
        'access.roles.delete' => 'Delete',

        'platform.settings.manage' => 'Manage',

        'access.notifications.user-created' => 'User created',
        'access.notifications.user-updated' => 'User updated',
        'access.notifications.user-deleted' => 'User deleted',
        'access.notifications.role-created' => 'Role created',
        'access.notifications.role-updated' => 'Role updated',
        'access.notifications.role-deleted' => 'Role deleted',
    ],

    'descriptions' => [
        'access.users.view' => 'Allows listing and viewing user details.',
        'access.users.create' => 'Allows creating new users in the system.',
        'access.users.update' => 'Allows modifying existing user data.',
        'access.users.delete' => 'Allows deactivating users from the system.',
        'access.users.restore' => 'Allows reactivating previously deleted users.',

        'access.roles.view' => 'Allows viewing roles and their assigned permissions.',
        'access.roles.create' => 'Allows creating new roles and assigning permissions.',
        'access.roles.update' => 'Allows modifying the name and permissions of an existing role.',
        'access.roles.delete' => 'Allows deleting roles with no assigned users.',

        'platform.settings.manage' => 'Allows changing the application name, logo and favicon.',

        'access.notifications.user-created' => 'Receive a notification when a new user is created.',
        'access.notifications.user-updated' => 'Receive a notification when a user is updated.',
        'access.notifications.user-deleted' => 'Receive a notification when a user is deleted.',
        'access.notifications.role-created' => 'Receive a notification when a new role is created.',
        'access.notifications.role-updated' => 'Receive a notification when a role\'s permissions are modified.',
        'access.notifications.role-deleted' => 'Receive a notification when a role is deleted.',
    ],

];
