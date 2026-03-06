<?php

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

Route::get('/dashboard', fn () => view('app.general.dashboard.index'))
    ->middleware('verified')
    ->name('dashboard');

Route::get('/settings', fn () => view('app.general.settings.index'))
    ->name('settings');

