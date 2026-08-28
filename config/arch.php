<?php

declare(strict_types=1);

return [

    /**
     * Tablas exentas del prefijo de módulo (R25) y de la clave UUID (R30).
     *
     * Una sola lista, leída por las dos reglas. Dos copias es lo que hace que
     * una se quede corta: la de R30 no incluía `media`, `passkeys` ni
     * `failed_jobs`, y habría marcado tres tablas de paquete el día que el
     * check existiera.
     *
     * Son de paquetes y de infraestructura: cambiarlas no compra nada y rompe
     * lo que las espera con ese nombre.
     */
    'exempt_tables' => [
        'users',
        'roles',
        'permissions',
        'model_has_roles',
        'model_has_permissions',
        'role_has_permissions',
        'media',
        'audits',
        'passkeys',
        'personal_access_tokens',
        'notifications',
        'sessions',
        'cache',
        'cache_locks',
        'jobs',
        'job_batches',
        'failed_jobs',
        'password_reset_tokens',
        'migrations',
    ],

    /**
     * Tablas propias que además llevan clave entera (solo R30).
     *
     * Lista aparte y no dentro de `exempt_tables` porque las dos exenciones
     * dicen cosas distintas: aquella es «no es de ningún módulo», esta es «es
     * mía y no necesita UUID». Mezclarlas hacía que R32 dejara de mirar las FK
     * hacia estas tablas, que es justo lo que R32 existe para vigilar.
     *
     * `access_profiles` es una extensión 1:1 de `users`: se alcanza por el
     * usuario, nunca por su propio id. `platform_settings` es clave-valor
     * interna y no aparece en ninguna URL. Lo que R30 compra —que nadie
     * recorra los ids desde el navegador— no se cobra donde no hay ruta que
     * los exponga.
     */
    'integer_key_tables' => [
        'access_profiles',
        'platform_settings',
    ],

    /**
     * Prefijo válido por módulo, derivado de `app/Modules/`.
     *
     * Se calcula y no se escribe: una lista a mano se queda corta en cuanto
     * alguien crea un módulo, y los checks que la leen dejarían de mirar sus
     * tablas sin decir nada.
     */
    'module_prefixes' => array_map(
        'strtolower',
        array_map('basename', glob(dirname(__DIR__).'/app/Modules/*', GLOB_ONLYDIR) ?: [])
    ),
];
