<?php

return [

    /*
     |--------------------------------------------------------------------------
     | Menú principal
     |--------------------------------------------------------------------------
     |
     | Cada ítem puede tener:
     |   label       - Clave de traducción o texto (pasa por __())
     |   icon        - Nombre del icono HeroIcons (outline)
     |   route       - Nombre de la ruta Laravel
     |   active_route- Patrón wildcard para marcar activo (ej: 'dashboard.users.*')
     |   permission  - Permiso requerido (Spatie)
     |   permissions - Array de permisos; basta tener uno
     |   items       - Array de hijos → convierte el ítem en dropdown
     |
     | Regla de visibilidad con hijos:
     |   El padre se muestra si el usuario tiene permiso de al menos un hijo.
     |   Si el hijo tiene 'permission', el usuario debe tenerlo para verlo.
     |
     */
    'menu' => [
        [
            'label' => 'Dashboard',
            'icon'  => 'home',
            'route' => 'dashboard',
        ],

        // ── Personal ──────────────────────────────────────────────────────
        [
            'label'        => 'Personal',
            'icon'         => 'users',
            'active_route' => 'personal.*',
            'items'        => [
                [
                    'label'        => 'Usuarios',
                    'route'        => 'personal.usuarios.index',
                    'active_route' => 'personal.usuarios.*',
                    'permission'   => 'ver usuarios',
                ],
                [
                    'label'        => 'Roles y permisos',
                    'route'        => 'personal.roles.index',
                    'active_route' => 'personal.roles.*',
                    'permission'   => 'ver roles',
                ],
            ],
        ],

        // ── Operaciones ──────────────────────────────────────────────────
        // [
        //     'label'        => 'Operaciones',
        //     'icon'         => 'layout-grid',
        //     'active_route' => 'operations.*',
        //     'items' => [
        //         [
        //             'label'        => 'Dashboard',
        //             'route'        => 'operations.dashboard',
        //             'active_route' => 'operations.dashboard',
        //         ],
        //         [
        //             'label'        => 'Usuarios',
        //             'route'        => 'operations.users.index',
        //             'active_route' => 'operations.users.*',
        //             'permission'   => 'ver usuarios',
        //         ],
        //         [
        //             'label'        => 'Roles',
        //             'route'        => 'operations.roles.index',
        //             'active_route' => 'operations.roles.*',
        //             'permission'   => 'ver roles',
        //         ],
        //     ],
        // ],

        // ── Ventas ───────────────────────────────────────────────────────
        // [
        //     'label'        => 'Ventas',
        //     'icon'         => 'bar-chart-2',
        //     'active_route' => 'sales.*',
        //     'items' => [
        //         [
        //             'label'        => 'Dashboard',
        //             'route'        => 'sales.dashboard',
        //             'active_route' => 'sales.dashboard',
        //         ],
        //         [
        //             'label'        => 'Clientes',
        //             'route'        => 'sales.customers.index',
        //             'active_route' => 'sales.customers.*',
        //             'permission'   => 'ver clientes',
        //         ],
        //     ],
        // ],
    ],

];
