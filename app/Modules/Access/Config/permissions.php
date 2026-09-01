<?php

declare(strict_types=1);

return [

    /**
     * Roles
     *
     * Deben coincidir con los cases del enum App\Modules\Access\Enums\Roles.
     */
    'roles' => [
        'admin' => 'Administrador',
        'user' => 'Usuario',
    ],

    /**
     * Módulos por rol
     *
     * Módulos a los que cada rol tiene acceso completo por defecto.
     * El seeder asignará todos los permisos de cada módulo al rol.
     */
    'roles_modules' => [
        'admin' => [
            'usuarios',
            'roles',
            'sistema',
            'notif_usuarios',
            'notif_roles',
        ],
        'user' => [],
    ],

    /**
     * Módulos y permisos
     *
     * Primer nivel: módulos. Segundo nivel: permisos del módulo.
     * El seeder crea todos los permisos aquí definidos.
     */
    'permissions' => [
        'usuarios' => [
            'access.users.view',
            'access.users.create',
            'access.users.update',
            'access.users.delete',
            'access.users.restore',
        ],

        'roles' => [
            'access.roles.view',
            'access.roles.create',
            'access.roles.update',
            'access.roles.delete',
        ],

        'sistema' => [
            'platform.settings.manage',
        ],

        'notif_usuarios' => [
            'access.notifications.user-created',
            'access.notifications.user-updated',
            'access.notifications.user-deleted',
        ],

        'notif_roles' => [
            'access.notifications.role-created',
            'access.notifications.role-updated',
            'access.notifications.role-deleted',
        ],
    ],

    /**
     * Grupos de módulos
     *
     * Agrupa módulos bajo una sección visual en el formulario de roles.
     * Los módulos que no estén en ningún grupo aparecen en "Otros".
     */
    /**
     * El icono de cada grupo en la matriz de permisos.
     *
     * Un grupo sin entrada cae en la carpeta genérica: es una etiqueta, no una
     * regla, así que faltar no puede romper la pantalla.
     */
    'group_icons' => [
        'access' => 'lucide-shield',
        'configuracion' => 'lucide-settings',
        'notificaciones' => 'lucide-bell',
        'other' => 'lucide-folder',
    ],

    'module_groups' => [
        'access' => ['usuarios', 'roles'],
        'configuracion' => ['sistema'],
        'notificaciones' => ['notif_usuarios', 'notif_roles'],
    ],

];
