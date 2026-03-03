<?php

use App\Models\User;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('dashboard', function (BreadcrumbTrail $trail) {
    $trail->push('Dashboard', route('dashboard'));
});

// ── Personal › Usuarios ───────────────────────────────────────────────────

Breadcrumbs::for('personal.usuarios.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Usuarios', route('personal.usuarios.index'));
});

Breadcrumbs::for('personal.usuarios.create', function (BreadcrumbTrail $trail) {
    $trail->parent('personal.usuarios.index');
    $trail->push('Nuevo usuario', route('personal.usuarios.create'));
});

Breadcrumbs::for('personal.usuarios.edit', function (BreadcrumbTrail $trail, User $user) {
    $trail->parent('personal.usuarios.index');
    $trail->push($user->name, route('personal.usuarios.edit', $user));
});
