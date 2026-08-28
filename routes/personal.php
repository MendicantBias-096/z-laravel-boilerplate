<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;

/**
 * Dominio: Personal
 * Prefijo: /personal      Nombre base: personal.
 *
 * Gestión de usuarios, roles y permisos del sistema.
 */
Route::prefix('personal')->name('personal.')->group(function (): void {

    // Roles
    Route::prefix('roles')->name('roles.')->group(function (): void {

        Route::view('/', 'app.personal.roles.index')
            ->middleware('permission:ver roles')
            ->name('index');

        Route::view('/create', 'app.personal.roles.create')
            ->middleware('permission:crear roles')
            ->name('create');

        Route::middleware('permission:editar roles')
            ->get('/{role}/edit', fn (Role $role): Factory|View => view('app.personal.roles.edit', ['role' => $role]))
            ->name('edit');
    });

    // Usuarios
    Route::prefix('usuarios')->name('usuarios.')->group(function (): void {

        Route::view('/', 'app.personal.users.index')
            ->middleware('permission:ver usuarios')
            ->name('index');

        Route::view('/create', 'app.personal.users.create')
            ->middleware('permission:crear usuarios')
            ->name('create');

        Route::middleware('permission:editar usuarios')
            ->get('/{user}/edit', function (User $user): Factory|View {
                abort_if($user->id === auth()->id(), 403);
                abort_if($user->is_protected, 404);

                return view('app.personal.users.edit', ['user' => $user]);
            })
            ->name('edit');
    });
});
