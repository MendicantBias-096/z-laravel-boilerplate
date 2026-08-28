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
        'access.users.view' => 'Ver',
        'access.users.create' => 'Crear',
        'access.users.update' => 'Editar',
        'access.users.delete' => 'Eliminar',
        'access.users.restore' => 'Restaurar',

        'access.roles.view' => 'Ver',
        'access.roles.create' => 'Crear',
        'access.roles.update' => 'Editar',
        'access.roles.delete' => 'Eliminar',

        'platform.settings.manage' => 'Administrar',

        'access.notifications.user-created' => 'Usuario creado',
        'access.notifications.user-updated' => 'Usuario editado',
        'access.notifications.user-deleted' => 'Usuario eliminado',
        'access.notifications.role-created' => 'Rol creado',
        'access.notifications.role-updated' => 'Rol actualizado',
        'access.notifications.role-deleted' => 'Rol eliminado',
    ],

    /**
     * Descripciones de permisos (tooltip)
     */
    'descriptions' => [
        'access.users.view' => 'Permite listar y consultar el detalle de los usuarios del sistema.',
        'access.users.create' => 'Permite registrar nuevos usuarios en el sistema.',
        'access.users.update' => 'Permite modificar los datos de usuarios existentes.',
        'access.users.delete' => 'Permite desactivar usuarios del sistema.',
        'access.users.restore' => 'Permite reactivar usuarios previamente eliminados.',

        'access.roles.view' => 'Permite consultar los roles y sus permisos asignados.',
        'access.roles.create' => 'Permite crear nuevos roles y asignarles permisos.',
        'access.roles.update' => 'Permite modificar el nombre y permisos de un rol existente.',
        'access.roles.delete' => 'Permite eliminar roles que no tengan usuarios asignados.',

        'platform.settings.manage' => 'Permite cambiar el nombre, logo y favicon de la aplicación.',

        'access.notifications.user-created' => 'Recibe una notificación cuando se crea un nuevo usuario en el sistema.',
        'access.notifications.user-updated' => 'Recibe una notificación cuando se edita un usuario del sistema.',
        'access.notifications.user-deleted' => 'Recibe una notificación cuando se elimina un usuario del sistema.',
        'access.notifications.role-created' => 'Recibe una notificación cuando se crea un nuevo rol.',
        'access.notifications.role-updated' => 'Recibe una notificación cuando se modifican los permisos de un rol.',
        'access.notifications.role-deleted' => 'Recibe una notificación cuando se elimina un rol del sistema.',
    ],

];
