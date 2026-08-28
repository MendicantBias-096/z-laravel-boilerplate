<?php

declare(strict_types=1);

return [

    /**
     * Menú principal
     *
     * Cada ítem puede tener:
     *   label       - Clave de traducción o texto (pasa por __())
     *   icon        - Nombre del icono HeroIcons (outline)
     *   route       - Nombre de la ruta Laravel
     *   active_route- Patrón wildcard para marcar activo (ej: 'dashboard.users.*')
     *   permission  - Permiso requerido (Spatie)
     *   permissions - Array de permisos; basta tener uno
     *   items       - Array de hijos → convierte el ítem en dropdown
     *
     * Regla de visibilidad con hijos:
     *   El padre se muestra si el usuario tiene permiso de al menos un hijo.
     *   Si el hijo tiene 'permission', el usuario debe tenerlo para verlo.
     */
    'menu' => [
        [
            'label' => 'platform::menu.dashboard',
            'icon' => 'lucide-home',
            'route' => 'dashboard',
        ],

        // Personal
        [
            'label' => 'platform::menu.access',
            'icon' => 'lucide-users',
            'active_route' => 'access.*',
            'items' => [
                [
                    'label' => 'platform::menu.users',
                    'route' => 'access.users.index',
                    'active_route' => 'access.users.*',
                    'permission' => 'access.users.view',
                ],
                [
                    'label' => 'platform::menu.roles',
                    'route' => 'access.roles.index',
                    'active_route' => 'access.roles.*',
                    'permission' => 'access.roles.view',
                ],
            ],
        ],

        [
            'label' => 'platform::menu.docs',
            'icon' => 'lucide-book-open',
            'route' => 'platform.docs.index',
            'active_route' => 'platform.docs.*',
        ],

        // Operaciones
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
        //             'permission'   => 'access.users.view',
        //         ],
        //         [
        //             'label'        => 'Roles',
        //             'route'        => 'operations.roles.index',
        //             'active_route' => 'operations.roles.*',
        //             'permission'   => 'access.roles.view',
        //         ],
        //     ],
        // ],

        // Ventas
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
