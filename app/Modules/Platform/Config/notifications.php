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
            'permission' => 'access.notifications.user-created',
            'channels' => ['database'],
        ],

        'user_updated' => [
            'permission' => 'access.notifications.user-updated',
            'channels' => ['database'],
        ],

        'user_deleted' => [
            'permission' => 'access.notifications.user-deleted',
            'channels' => ['database'],
        ],

        'role_created' => [
            'permission' => 'access.notifications.role-created',
            'channels' => ['database'],
        ],

        'role_updated' => [
            'permission' => 'access.notifications.role-updated',
            'channels' => ['database'],
        ],

        'role_deleted' => [
            'permission' => 'access.notifications.role-deleted',
            'channels' => ['database'],
        ],

    ],

];
