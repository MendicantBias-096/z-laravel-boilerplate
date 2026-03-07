<?php

return [

    'groups' => [
        'personal'       => 'Personal',
        'configuracion'  => 'Configuration',
        'notificaciones' => 'Notifications',
        'other'          => 'Other',
    ],

    'modules' => [
        'usuarios'       => 'Users',
        'roles'          => 'Roles & permissions',
        'sistema'        => 'System',
        'notif_usuarios' => 'Users',
        'notif_roles'    => 'Roles',
    ],

    'permissions' => [
        'ver usuarios'        => 'View',
        'crear usuarios'      => 'Create',
        'editar usuarios'     => 'Edit',
        'eliminar usuarios'   => 'Delete',
        'restaurar usuarios'  => 'Restore',

        'ver roles'           => 'View',
        'crear roles'         => 'Create',
        'editar roles'        => 'Edit',
        'eliminar roles'      => 'Delete',

        'administrar sistema' => 'Manage',

        'notificacion usuario creado'    => 'User created',
        'notificacion usuario editado'   => 'User updated',
        'notificacion usuario eliminado' => 'User deleted',
        'notificacion rol creado'        => 'Role created',
        'notificacion rol actualizado'   => 'Role updated',
        'notificacion rol eliminado'     => 'Role deleted',
    ],

    'descriptions' => [
        'ver usuarios'        => 'Allows listing and viewing user details.',
        'crear usuarios'      => 'Allows creating new users in the system.',
        'editar usuarios'     => 'Allows modifying existing user data.',
        'eliminar usuarios'   => 'Allows deactivating users from the system.',
        'restaurar usuarios'  => 'Allows reactivating previously deleted users.',

        'ver roles'           => 'Allows viewing roles and their assigned permissions.',
        'crear roles'         => 'Allows creating new roles and assigning permissions.',
        'editar roles'        => 'Allows modifying the name and permissions of an existing role.',
        'eliminar roles'      => 'Allows deleting roles with no assigned users.',

        'administrar sistema' => 'Allows changing the application name, logo and favicon.',

        'notificacion usuario creado'    => 'Receive a notification when a new user is created.',
        'notificacion usuario editado'   => 'Receive a notification when a user is updated.',
        'notificacion usuario eliminado' => 'Receive a notification when a user is deleted.',
        'notificacion rol creado'        => 'Receive a notification when a new role is created.',
        'notificacion rol actualizado'   => 'Receive a notification when a role\'s permissions are modified.',
        'notificacion rol eliminado'     => 'Receive a notification when a role is deleted.',
    ],

];
