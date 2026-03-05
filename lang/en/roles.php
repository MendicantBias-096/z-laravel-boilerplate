<?php

return [

    'groups' => [
        'personal'      => 'Personal',
        'configuracion' => 'Configuration',
        'other'         => 'Other',
    ],

    'modules' => [
        'usuarios' => 'Users',
        'roles'    => 'Roles & permissions',
        'sistema'  => 'System',
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
    ],

];
