<?php

return [

    /*
     |--------------------------------------------------------------------------
     | Roles
     |--------------------------------------------------------------------------
     |
     | Deben coincidir con los cases del enum App\Enums\Roles.
     |
     */
    'roles' => [
        'admin',
        'user',
    ],

    /*
     |--------------------------------------------------------------------------
     | Módulos por rol
     |--------------------------------------------------------------------------
     |
     | Módulos a los que cada rol tiene acceso completo por defecto.
     | El seeder asignará todos los permisos de cada módulo al rol.
     |
     */
    'roles_modules' => [
        'admin' => [
            'usuarios',
        ],
        'user' => [],
    ],

    /*
     |--------------------------------------------------------------------------
     | Módulos y permisos
     |--------------------------------------------------------------------------
     |
     | Primer nivel: módulos. Segundo nivel: permisos del módulo.
     | El seeder crea todos los permisos aquí definidos.
     |
     */
    'permissions' => [
        'usuarios' => [
            'ver usuarios',
            'crear usuarios',
            'editar usuarios',
            'eliminar usuarios',
        ],

        /*
         |----------------------------------------------------------------------
         | Módulos adicionales (descomentar según necesidad)
         |----------------------------------------------------------------------
         */

        // 'roles' => [
        //     'ver roles',
        //     'crear roles',
        //     'editar roles',
        //     'eliminar roles',
        // ],
    ],

];
