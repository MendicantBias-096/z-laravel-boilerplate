<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Módulo: Operaciones
|--------------------------------------------------------------------------
| Prefijo: /operations        Nombre base: operations.
|
| Patrón para nuevas rutas:
|   Route::get('/users', \App\Livewire\App\Operations\Users\Index::class)
|       ->name('users.index');
*/

Route::prefix('operations')->name('operations.')->group(function () {

    Route::get('/dashboard', \App\Livewire\App\Operations\Dashboard::class)
        ->name('dashboard');

    // Route::get('/users',    \App\Livewire\App\Operations\Users\Index::class)->name('users.index');
    // Route::get('/roles',    \App\Livewire\App\Operations\Roles\Index::class)->name('roles.index');
    // Route::get('/settings', \App\Livewire\App\Operations\Settings\Index::class)->name('settings.index');

});
