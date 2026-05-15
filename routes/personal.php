<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dominio: Personal
|--------------------------------------------------------------------------
| Prefijo: /personal      Nombre base: personal.
|
| Gestión de usuarios, roles y permisos del sistema.
*/

Route::prefix('personal')->name('personal.')->group(function (): void {

    // ── Roles ─────────────────────────────────────────────────────────────
    Route::prefix('roles')->name('roles.')->group(function (): void {

        Route::middleware('permission:ver roles')
            ->get('/', fn (): Factory|\Illuminate\Contracts\View\View => view('app.personal.roles.index'))
            ->name('index');

        Route::middleware('permission:crear roles')
            ->get('/create', fn (): Factory|\Illuminate\Contracts\View\View => view('app.personal.roles.create'))
            ->name('create');

        Route::middleware('permission:editar roles')
            ->get('/{role}/edit', fn (Role $role): Factory|\Illuminate\Contracts\View\View => view('app.personal.roles.edit', ['role' => $role]))
            ->name('edit');
    });

    // ── Usuarios ──────────────────────────────────────────────────────────
    Route::prefix('usuarios')->name('usuarios.')->group(function (): void {

        Route::middleware('permission:ver usuarios')
            ->get('/', fn (): Factory|\Illuminate\Contracts\View\View => view('app.personal.users.index'))
            ->name('index');

        Route::middleware('permission:crear usuarios')
            ->get('/create', fn (): Factory|\Illuminate\Contracts\View\View => view('app.personal.users.create'))
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
