<?php

declare(strict_types=1);

use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Módulo: General
|--------------------------------------------------------------------------
| Prefijo: /          Nombre base: (sin prefijo, rutas globales del app)
|
| Aquí van las rutas del área autenticada que no pertenecen
| a ningún dominio específico (dashboard home, perfil, etc.)
*/

Route::get('/dashboard', fn (): Factory|\Illuminate\Contracts\View\View => view('app.general.dashboard.index'))
    ->middleware('verified')
    ->name('dashboard');

Route::get('/settings', fn (): Factory|\Illuminate\Contracts\View\View => view('app.general.settings.index'))
    ->name('settings');

Route::get('/notifications', fn (): Factory|\Illuminate\Contracts\View\View => view('app.general.notifications.index'))
    ->name('general.notifications.index');
