<?php

declare(strict_types=1);

return [

    /**
     * Proxies en los que se confía para leer las cabeceras `X-Forwarded-*`.
     *
     * Sin esto, detrás de un balanceador `$request->isSecure()` devuelve
     * `false` aunque el usuario haya llegado por `https://`, y de ahí salen
     * dos fallos que nadie ve: la cookie de sesión se emite sin la marca
     * `Secure` —porque `config/session.php` la deriva del request— y todo
     * `route()` y `asset()` genera URLs `http://`.
     *
     * `'*'` confía en cualquier proxy, que es lo correcto cuando la IP del
     * balanceador es dinámica —ELB, Cloudflare, Laravel Cloud— y **solo** si
     * nada puede hablar con la app saltándoselo. Si la app es alcanzable
     * directamente, un cliente puede mandar `X-Forwarded-For` y suplantar su
     * IP: ahí va la lista concreta, separada por comas.
     */
    'trusted_proxies' => env('TRUSTED_PROXIES', '*'),

    /**
     * HSTS. Solo se envía sobre una conexión ya segura: mandarlo por `http://`
     * no hace nada, y mandarlo desde un `localhost` de desarrollo deja el
     * dominio clavado a https en el navegador del desarrollador durante un año.
     *
     * `preload` queda en `false` a propósito. Entrar en la lista de precarga de
     * los navegadores es una decisión que no se deshace en el plazo de un
     * despliegue, y se toma por proyecto, no por boilerplate.
     */
    'hsts' => [
        'max_age' => (int) env('HSTS_MAX_AGE', 31536000),
        'include_subdomains' => (bool) env('HSTS_INCLUDE_SUBDOMAINS', true),
        'preload' => (bool) env('HSTS_PRELOAD', false),
    ],

    /**
     * Content Security Policy.
     *
     * Bloquea. Nació en `Report-Only` —Livewire y Alpine evalúan código en
     * tiempo de ejecución, y una política estricta no degrada la interfaz, la
     * rompe entera— y pasó a bloqueo cuando las 13 pantallas de la aplicación
     * dieron cero violaciones con un listener de `securitypolicyviolation`.
     *
     * **Un proyecto hijo que añada un origen —un CDN, un mapa, un iframe de
     * pago— vuelve a `CSP_REPORT_ONLY=true` mientras lo mide.** Endurecer sin
     * haber recogido es exactamente lo que rompe la interfaz en el primer
     * despliegue.
     *
     * `'unsafe-eval'` lo pide Alpine, que compila cada expresión `x-on:` con
     * `new Function`. `'unsafe-inline'` en `style-src` lo piden los estilos
     * que Alpine y TallStackUI escriben en el atributo `style`. Los dos son
     * el precio del stack, no un descuido: mientras estén, la CSP protege
     * contra la carga de scripts de otro origen, no contra el XSS inyectado.
     *
     * Los dos orígenes externos salieron de recoger los reportes del navegador,
     * no de suponerlos: `fonts.bunny.net` sirve la hoja y los ficheros de Inter
     * en los tres layouts públicos, y `fonts.gstatic.com` las imágenes de emoji
     * del componente `reaction` de TallStackUI. Un proyecto hijo que sirva sus
     * fuentes desde `public/` puede quitar los dos y quedarse en `'self'`.
     */
    'csp' => [
        'report_only' => (bool) env('CSP_REPORT_ONLY', false),

        'directives' => [
            'default-src' => "'self'",
            'script-src' => "'self' 'unsafe-eval' 'unsafe-inline'",
            'style-src' => "'self' 'unsafe-inline' https://fonts.bunny.net",
            'img-src' => "'self' data: blob: https://fonts.gstatic.com",
            'font-src' => "'self' data: https://fonts.bunny.net",
            'connect-src' => "'self' ws: wss:",
            'media-src' => "'self' data: blob:",
            'frame-ancestors' => "'none'",
            'base-uri' => "'self'",
            'form-action' => "'self'",
            'object-src' => "'none'",
        ],
    ],

];
