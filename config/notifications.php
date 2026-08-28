<?php

declare(strict_types=1);

return [

    /**
     * Eventos notificables
     *
     * Cada clave es un identificador único del evento. Al disparar una
     * notificación desde código se usa este identificador para resolver
     * a qué usuarios enviarla y por qué canales.
     *
     * Estructura de cada evento:
     *   - permission : permiso que el usuario/rol debe tener para recibirla
     *   - channels   : canales habilitados ['database', 'mail']
     *   - class      : clase Notification que se instancia (opcional, para referencia)
     */
    'events' => [

        'user_created' => [
            'permission' => 'notificacion usuario creado',
            'channels' => ['database'],
        ],

        'user_updated' => [
            'permission' => 'notificacion usuario editado',
            'channels' => ['database'],
        ],

        'user_deleted' => [
            'permission' => 'notificacion usuario eliminado',
            'channels' => ['database'],
        ],

        'role_created' => [
            'permission' => 'notificacion rol creado',
            'channels' => ['database'],
        ],

        'role_updated' => [
            'permission' => 'notificacion rol actualizado',
            'channels' => ['database'],
        ],

        'role_deleted' => [
            'permission' => 'notificacion rol eliminado',
            'channels' => ['database'],
        ],

    ],

];
