<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Las cabeceras de seguridad de toda respuesta web.
 *
 * Antes de esto la aplicación no enviaba ninguna de las cinco, comprobado con
 * `curl -D -` contra el entorno vivo. Van en un middleware y no en el servidor
 * web porque el boilerplate no sabe bajo qué nginx va a acabar cada proyecto
 * hijo, y una cabecera que depende del despliegue es una cabecera que falta.
 *
 * Lo que cada una compra:
 *
 *   X-Content-Type-Options  el navegador no adivina el tipo de un archivo
 *                           subido, así que un .txt no se ejecuta como script
 *   X-Frame-Options         nadie embebe la app en un iframe para robar
 *                           clics sobre botones que el usuario no ve
 *   Referrer-Policy         una URL con un token en el path no viaja en el
 *                           `Referer` hacia un dominio ajeno
 *   Strict-Transport-Sec.   el navegador no vuelve a intentar `http://`
 *   Content-Security-Policy de dónde puede cargar recursos la página
 */
class SecurityHeaders
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', $this->hsts());
        }

        $header = config('security.csp.report_only') === true
            ? 'Content-Security-Policy-Report-Only'
            : 'Content-Security-Policy';

        $response->headers->set($header, $this->csp());

        return $response;
    }

    /**
     * `isSecure()` es lo que decide, y depende de `trusted_proxies`: detrás de
     * un balanceador sin proxies confiados devuelve `false` y esta cabecera
     * nunca sale. Las dos mitades del ticket son la misma.
     */
    private function hsts(): string
    {
        $value = 'max-age='.config('security.hsts.max_age');

        if (config('security.hsts.include_subdomains') === true) {
            $value .= '; includeSubDomains';
        }

        if (config('security.hsts.preload') === true) {
            $value .= '; preload';
        }

        return $value;
    }

    private function csp(): string
    {
        /** @var array<string, string> $directives */
        $directives = config('security.csp.directives', []);

        $parts = [];

        foreach ($directives as $name => $value) {
            $parts[] = trim($name.' '.$value);
        }

        return implode('; ', $parts);
    }
}
