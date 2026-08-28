<?php

declare(strict_types=1);

return [

    /**
     * Roles
     *
     * Deben coincidir con los cases del enum App\Enums\Roles.
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
            'ver usuarios',
            'crear usuarios',
            'editar usuarios',
            'eliminar usuarios',
            'restaurar usuarios',
        ],

        'roles' => [
            'ver roles',
            'crear roles',
            'editar roles',
            'eliminar roles',
        ],

        'sistema' => [
            'administrar sistema',
        ],

        'notif_usuarios' => [
            'notificacion usuario creado',
            'notificacion usuario editado',
            'notificacion usuario eliminado',
        ],

        'notif_roles' => [
            'notificacion rol creado',
            'notificacion rol actualizado',
            'notificacion rol eliminado',
        ],
    ],

    /**
     * Grupos de módulos
     *
     * Agrupa módulos bajo una sección visual en el formulario de roles.
     * Los módulos que no estén en ningún grupo aparecen en "Otros".
     */
    'module_groups' => [
        'personal' => ['usuarios', 'roles'],
        'configuracion' => ['sistema'],
        'notificaciones' => ['notif_usuarios', 'notif_roles'],
    ],

];
