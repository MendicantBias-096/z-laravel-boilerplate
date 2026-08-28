<?php

return [

    'title' => 'Roles',
    'create' => 'Nuevo rol',
    'users' => 'Usuarios',
    'new_user' => 'Nuevo usuario',

    /**
     * Grupos de módulos
     */
    'groups' => [
        'access' => 'Accesos',
        'configuracion' => 'Configuración',
        'notificaciones' => 'Notificaciones',
        'other' => 'Otros',
    ],

    /**
     * Etiquetas de módulos
     */
    'modules' => [
        'usuarios' => 'Usuarios',
        'roles' => 'Roles y permisos',
        'sistema' => 'Sistema',
        'notif_usuarios' => 'Usuarios',
        'notif_roles' => 'Roles',
    ],

    /**
     * Etiquetas de permisos
     * Solo el verbo/acción — el módulo se muestra como cabecera de sección.
     */
    'permissions' => [
        'ver usuarios' => 'Ver',
        'crear usuarios' => 'Crear',
        'editar usuarios' => 'Editar',
        'eliminar usuarios' => 'Eliminar',
        'restaurar usuarios' => 'Restaurar',

        'ver roles' => 'Ver',
        'crear roles' => 'Crear',
        'editar roles' => 'Editar',
        'eliminar roles' => 'Eliminar',

        'administrar sistema' => 'Administrar',

        'notificacion usuario creado' => 'Usuario creado',
        'notificacion usuario editado' => 'Usuario editado',
        'notificacion usuario eliminado' => 'Usuario eliminado',
        'notificacion rol creado' => 'Rol creado',
        'notificacion rol actualizado' => 'Rol actualizado',
        'notificacion rol eliminado' => 'Rol eliminado',
    ],

    /**
     * Descripciones de permisos (tooltip)
     */
    'descriptions' => [
        'ver usuarios' => 'Permite listar y consultar el detalle de los usuarios del sistema.',
        'crear usuarios' => 'Permite registrar nuevos usuarios en el sistema.',
        'editar usuarios' => 'Permite modificar los datos de usuarios existentes.',
        'eliminar usuarios' => 'Permite desactivar usuarios del sistema.',
        'restaurar usuarios' => 'Permite reactivar usuarios previamente eliminados.',

        'ver roles' => 'Permite consultar los roles y sus permisos asignados.',
        'crear roles' => 'Permite crear nuevos roles y asignarles permisos.',
        'editar roles' => 'Permite modificar el nombre y permisos de un rol existente.',
        'eliminar roles' => 'Permite eliminar roles que no tengan usuarios asignados.',

        'administrar sistema' => 'Permite cambiar el nombre, logo y favicon de la aplicación.',

        'notificacion usuario creado' => 'Recibe una notificación cuando se crea un nuevo usuario en el sistema.',
        'notificacion usuario editado' => 'Recibe una notificación cuando se edita un usuario del sistema.',
        'notificacion usuario eliminado' => 'Recibe una notificación cuando se elimina un usuario del sistema.',
        'notificacion rol creado' => 'Recibe una notificación cuando se crea un nuevo rol.',
        'notificacion rol actualizado' => 'Recibe una notificación cuando se modifican los permisos de un rol.',
        'notificacion rol eliminado' => 'Recibe una notificación cuando se elimina un rol del sistema.',
    ],

];
