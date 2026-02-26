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

Route::get('/dashboard', \App\Livewire\App\General\Dashboard::class)
    ->name('dashboard');
