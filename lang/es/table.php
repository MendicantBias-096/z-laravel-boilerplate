<?php

return [
    'actions' => [
        'edit'    => 'Editar',
        'delete'  => 'Eliminar',
        'restore' => 'Restaurar',
    ],
    'tooltips' => [
        'edit'    => 'Editar :model',
        'delete'  => 'Eliminar :model',
        'restore' => 'Restaurar :model',
    ],
    'confirm' => [
        'delete'  => '¿Eliminar este :model?',
        'restore' => '¿Restaurar este :model?',
    ],
    'dialog' => [
        'delete' => [
            'title'       => '¿Eliminar :model?',
            'description' => 'Esta acción no se puede deshacer.',
            'confirm'     => 'Sí, eliminar',
        ],
        'restore' => [
            'title'       => '¿Restaurar :model?',
            'description' => 'El registro volverá a estar activo.',
            'confirm'     => 'Sí, restaurar',
        ],
        'cancel' => 'Cancelar',
    ],
    'new' => 'Nuevo :model',
];
