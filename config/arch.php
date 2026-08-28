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

    /** Prefijo válido por módulo, derivado de app/Modules/. */
    'module_prefixes' => ['access', 'platform'],
];
