<?php

return [
    'actions' => [
        'edit' => 'Editar',
        'delete' => 'Eliminar',
        'restore' => 'Restaurar',
    ],
    'tooltips' => [
        'edit' => 'Editar :model',
        'delete' => 'Eliminar :model',
        'restore' => 'Restaurar :model',
    ],
    'confirm' => [
        'delete' => '¿Eliminar este :model?',
        'restore' => '¿Restaurar este :model?',
    ],
    'dialog' => [
        'delete' => [
            'title' => '¿Eliminar :model?',
            'description' => 'Esta acción no se puede deshacer.',
            'confirm' => 'Sí, eliminar',
        ],
        'restore' => [
            'title' => '¿Restaurar :model?',
            'description' => 'El registro volverá a estar activo.',
            'confirm' => 'Sí, restaurar',
        ],
        'cancel' => 'Cancelar',
    ],
    'new' => 'Nuevo :model',
    'search' => 'Buscar...',
    'filters' => 'Filtros',
    'active' => 'activo|activos',
    'clear' => 'Limpiar filtros',
    'showing' => 'Mostrando',
    'results' => 'resultados de',
    'permission' => 'permiso|permisos',

    // Usuarios
    'users' => [
        'headers' => [
            'username' => 'Usuario',
            'name' => 'Nombre',
            'email' => 'Correo',
            'role' => 'Rol',
            'permissions' => 'Permisos',
            'status' => 'Estado',
            'actions' => 'Acciones',
        ],
        'filter_email' => 'Correo',
        'filter_email_placeholder' => 'Filtrar por correo...',
        'status_active' => 'Activo',
        'status_inactive' => 'Desactivado',
        'status_deleted' => 'Eliminado',
    ],

    // Roles
    'roles' => [
        'headers' => [
            'name' => 'Nombre',
            'identifier' => 'Identificador',
            'permissions' => 'Permisos',
            'users' => 'Usuarios',
            'actions' => 'Acciones',
        ],
    ],
];
