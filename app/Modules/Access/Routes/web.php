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
            ->middleware('permission:ver roles')
            ->name('index');

        Route::view('/create', 'access::roles.create')
            ->middleware('permission:crear roles')
            ->name('create');

        Route::middleware('permission:editar roles')
            ->get('/{role}/edit', fn (Role $role): Factory|View => view('access::roles.edit', ['role' => $role]))
            ->name('edit');
    });

    Route::prefix('users')->name('users.')->group(function (): void {

        Route::view('/', 'access::users.index')
            ->middleware('permission:ver usuarios')
            ->name('index');

        Route::view('/create', 'access::users.create')
            ->middleware('permission:crear usuarios')
            ->name('create');

        Route::middleware('permission:editar usuarios')
            ->get('/{user}/edit', function (User $user): Factory|View {
                // Deuda declarada: estas dos son decisiones de autorización y
                // R39 las quiere en una Policy. Mudarlas exige antes resolver
                // que `Gate::before` corta para el rol admin, que es justo el
                // actor al que estas guardas también atan.
                abort_if($user->id === auth()->id(), 403);
                abort_if($user->is_protected, 404);

                return view('access::users.edit', ['user' => $user]);
            })
            ->name('edit');
    });
});
