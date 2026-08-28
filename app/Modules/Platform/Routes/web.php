<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/**
 * Platform · el área autenticada que no pertenece a ningún negocio.
 *
 * Sin prefijo de URL: los módulos de plataforma no exponen su nombre de
 * arquitectura a un humano. Los de negocio sí prefijan, para no colisionar.
 *
 * El middleware viaja con las rutas y no en `bootstrap/app.php`, porque es
 * parte de lo que el módulo declara sobre sí mismo.
 */
Route::middleware(['web', 'auth'])->group(function (): void {

    Route::view('/dashboard', 'platform::dashboard.index')
        ->middleware('verified')
        ->name('dashboard');

    Route::view('/settings', 'platform::settings.index')
        ->name('settings');

    Route::view('/notifications', 'platform::notifications.index')
        ->name('platform.notifications.index');

    Route::view('/docs', 'platform::docs.index')
        ->name('platform.docs.index');
});
