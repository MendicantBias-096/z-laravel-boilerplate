<?php

use App\Http\Middleware\LogRequestDuration;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\ServeMarkdownTwin;
use App\Http\Middleware\SetLocale;
use App\Modules\Access\Http\Middleware\EnsureUserIsActive;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        // Las rutas y los breadcrumbs de un módulo los carga su propio
        // ServiceProvider, así que aquí ya no queda nada que agrupar.
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // `config()` todavía no está cargada cuando corre este closure, así
        // que el valor se lee del entorno aquí y `config/security.php` lo
        // documenta. Sin proxies confiados, `isSecure()` miente detrás de un
        // balanceador y se caen con él la cookie `Secure` y el HSTS.
        $proxies = env('TRUSTED_PROXIES', '*');
        $middleware->trustProxies(
            at: $proxies === '*' ? '*' : array_map(trim(...), explode(',', (string) $proxies)),
        );

        $middleware->encryptCookies(except: ['darkMode', 'locale']);
        $middleware->redirectGuestsTo('/login');
        $middleware->statefulApi();

        // Global y no en el grupo `web`: el sufijo `.md` se quita antes de que
        // el router busque la ruta, porque `/nosotros.md` no casa con ninguna.
        $middleware->prepend(ServeMarkdownTwin::class);

        $middleware->web(append: [
            SecurityHeaders::class,
            SetLocale::class,
            EnsureUserIsActive::class,
            LogRequestDuration::class,
        ]);

        $middleware->alias([
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
