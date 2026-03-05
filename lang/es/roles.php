<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Grupos de módulos
    |--------------------------------------------------------------------------
    */
    'groups' => [
        'personal'      => 'Personal',
        'configuracion' => 'Configuración',
        'other'         => 'Otros',
    ],

    /*
    |--------------------------------------------------------------------------
    | Etiquetas de módulos
    |--------------------------------------------------------------------------
    */
    'modules' => [
        'usuarios' => 'Usuarios',
        'roles'    => 'Roles y permisos',
        'sistema'  => 'Sistema',
    ],

    /*
    |--------------------------------------------------------------------------
    | Etiquetas de permisos
    |--------------------------------------------------------------------------
    | Solo el verbo/acción — el módulo se muestra como cabecera de sección.
    */
    'permissions' => [
        'ver usuarios'        => 'Ver',
        'crear usuarios'      => 'Crear',
        'editar usuarios'     => 'Editar',
        'eliminar usuarios'   => 'Eliminar',
        'restaurar usuarios'  => 'Restaurar',

        'ver roles'           => 'Ver',
        'crear roles'         => 'Crear',
        'editar roles'        => 'Editar',
        'eliminar roles'      => 'Eliminar',

        'administrar sistema' => 'Administrar',
    ],

    /*
    |--------------------------------------------------------------------------
    | Descripciones de permisos (tooltip)
    |--------------------------------------------------------------------------
    */
    'descriptions' => [
        'ver usuarios'        => 'Permite listar y consultar el detalle de los usuarios del sistema.',
        'crear usuarios'      => 'Permite registrar nuevos usuarios en el sistema.',
        'editar usuarios'     => 'Permite modificar los datos de usuarios existentes.',
        'eliminar usuarios'   => 'Permite desactivar usuarios del sistema.',
        'restaurar usuarios'  => 'Permite reactivar usuarios previamente eliminados.',

        'ver roles'           => 'Permite consultar los roles y sus permisos asignados.',
        'crear roles'         => 'Permite crear nuevos roles y asignarles permisos.',
        'editar roles'        => 'Permite modificar el nombre y permisos de un rol existente.',
        'eliminar roles'      => 'Permite eliminar roles que no tengan usuarios asignados.',

        'administrar sistema' => 'Permite cambiar el nombre, logo y favicon de la aplicación.',
    ],

];
