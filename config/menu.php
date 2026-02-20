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

        // Ejemplo ítem simple con permiso:
        // [
        //     'label'        => 'Usuarios',
        //     'icon'         => 'users',
        //     'route'        => 'dashboard.users.index',
        //     'active_route' => 'dashboard.users.*',
        //     'permission'   => 'ver usuarios',
        // ],

        // Ejemplo con hijos (dropdown):
        // [
        //     'label'        => 'Gestión',
        //     'icon'         => 'squares-2x2',
        //     'active_route' => 'dashboard.gestion.*',
        //     'items' => [
        //         [
        //             'label'        => 'Usuarios',
        //             'route'        => 'dashboard.users.index',
        //             'active_route' => 'dashboard.users.*',
        //             'permission'   => 'ver usuarios',
        //         ],
        //         [
        //             'label'        => 'Roles',
        //             'route'        => 'dashboard.roles.index',
        //             'active_route' => 'dashboard.roles.*',
        //             'permission'   => 'ver roles',
        //         ],
        //     ],
        // ],
    ],

];
