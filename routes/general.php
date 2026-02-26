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
    ->name('dashboard');

Route::get('/settings', fn () => view('app.general.settings.index'))
    ->name('settings');

Route::get('/support', fn () => view('app.general.support.index'))
    ->name('support');
