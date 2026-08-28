<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogRequestDuration
{
    /**
     * Umbral (ms) a partir del cual una request se considera lenta.
     * En local se loguea todo; fuera de local, solo lo que supere esto.
     *
     * ponytail: umbral fijo; muévelo a config solo si necesitas ajustarlo por entorno.
     */
    private const SLOW_THRESHOLD_MS = 500;

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);
        $response = $next($request);
        $durationMs = round((microtime(true) - $start) * 1000, 1);

        if (app()->isLocal() || $durationMs >= self::SLOW_THRESHOLD_MS) {
            Log::info('request.duration', [
                'ms' => $durationMs,
                'method' => $request->method(),
                // El patrón, no la URL: `/reset-password/{token}` en vez del
                // token real. `fullUrl()` dejaba una credencial de un solo uso
                // escrita en storage/logs, y de paso agrupa mejor por ruta.
                'route' => $request->route()?->uri() ?? $request->path(),
                'status' => $response->getStatusCode(),
            ]);
        }

        return $response;
    }
}
