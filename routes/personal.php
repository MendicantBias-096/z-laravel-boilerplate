<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dominio: Personal
|--------------------------------------------------------------------------
| Prefijo: /personal      Nombre base: personal.
|
| Gestión de usuarios, roles y permisos del sistema.
*/

Route::prefix('personal')->name('personal.')->group(function () {

    // ── Usuarios ──────────────────────────────────────────────────────────
    Route::prefix('usuarios')->name('usuarios.')->group(function () {

        Route::middleware('permission:ver usuarios')
            ->get('/', fn () => view('app.personal.users.index'))
            ->name('index');

        Route::middleware('permission:crear usuarios')
            ->get('/create', fn () => view('app.personal.users.create'))
            ->name('create');

        Route::middleware('permission:editar usuarios')
            ->get('/{user}/edit', fn (User $user) => view('app.personal.users.edit', ['user' => $user]))
            ->name('edit');
    });
});
