<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Eventos notificables
    |--------------------------------------------------------------------------
    |
    | Cada clave es un identificador único del evento. Al disparar una
    | notificación desde código se usa este identificador para resolver
    | a qué usuarios enviarla y por qué canales.
    |
    | Estructura de cada evento:
    |   - permission : permiso que el usuario/rol debe tener para recibirla
    |   - channels   : canales habilitados ['database', 'mail']
    |   - class      : clase Notification que se instancia (opcional, para referencia)
    |
    */

    'events' => [

        'user.created' => [
            'permission' => 'notificacion usuario creado',
            'channels'   => ['database'],
        ],

        'user.deleted' => [
            'permission' => 'notificacion usuario eliminado',
            'channels'   => ['database'],
        ],

        'role.created' => [
            'permission' => 'notificacion rol creado',
            'channels'   => ['database'],
        ],

        'role.updated' => [
            'permission' => 'notificacion rol actualizado',
            'channels'   => ['database'],
        ],

    ],

];
