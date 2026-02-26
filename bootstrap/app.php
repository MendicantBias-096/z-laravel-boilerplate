<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware(['web', 'auth'])
                ->group(base_path('routes/general.php'));

            Route::middleware(['web', 'auth'])
                ->group(base_path('routes/operations.php'));

            Route::middleware(['web', 'auth'])
                ->group(base_path('routes/sales.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(except: ['darkMode']);
        $middleware->redirectGuestsTo('/login');
        $middleware->statefulApi();

        $middleware->alias([
            'abilities'          => \Laravel\Sanctum\Http\Middleware\CheckAbilities::class,
            'ability'            => \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility::class,
            'role'               => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'         => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
