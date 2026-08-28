<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/**
 * Módulo: General
 * Prefijo: /          Nombre base: (sin prefijo, rutas globales del app)
 *
 * Aquí van las rutas del área autenticada que no pertenecen
 * a ningún dominio específico (dashboard home, perfil, etc.)
 */
Route::view('/dashboard', 'app.general.dashboard.index')
    ->middleware('verified')
    ->name('dashboard');

Route::view('/settings', 'app.general.settings.index')
    ->name('settings');

Route::view('/notifications', 'app.general.notifications.index')
    ->name('general.notifications.index');

Route::view('/docs', 'app.general.docs.index')
    ->name('general.docs.index');
