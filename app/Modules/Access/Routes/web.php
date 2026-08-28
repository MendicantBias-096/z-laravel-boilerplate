<?php

declare(strict_types=1);

use App\Modules\Access\Models\Role;
use App\Modules\Access\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;

/**
 * Access · identidad: usuarios, roles y permisos.
 *
 * Sin prefijo de URL —`/users`, no `/access/users`— porque un módulo de
 * plataforma no expone su nombre de arquitectura a un humano. El namespace
 * refleja la arquitectura; la URL refleja el producto (R5).
 */
Route::middleware(['web', 'auth', 'verified'])->name('access.')->group(function (): void {

    Route::prefix('roles')->name('roles.')->group(function (): void {

        Route::view('/', 'access::roles.index')
            ->middleware('permission:access.roles.view')
            ->name('index');

        Route::view('/create', 'access::roles.create')
            ->middleware('permission:access.roles.create')
            ->name('create');

        Route::middleware('permission:access.roles.update')
            ->get('/{role}/edit', fn (Role $role): Factory|View => view('access::roles.edit', ['role' => $role]))
            ->name('edit');
    });

    Route::prefix('users')->name('users.')->group(function (): void {

        Route::view('/', 'access::users.index')
            ->middleware('permission:access.users.view')
            ->name('index');

        Route::view('/create', 'access::users.create')
            ->middleware('permission:access.users.create')
            ->name('create');

        // `permission:` es la puerta gruesa —quién puede editar usuarios— y
        // `can:` la decisión sobre este usuario concreto, que vive en
        // `UserPolicy::update()` (R39).
        Route::middleware(['permission:access.users.update', 'can:update,user'])
            ->get('/{user}/edit', fn (User $user): Factory|View => view(
                'access::users.edit',
                ['user' => $user]
            ))
            ->name('edit');
    });
});
