<?php

use App\Models\User;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// ── Personal ──────────────────────────────────────────────────────────────

Breadcrumbs::for('personal', function (BreadcrumbTrail $trail) {
    $trail->push('Personal'); // sin URL — solo etiqueta de dominio
});

// ── Personal › Roles ──────────────────────────────────────────────────────

Breadcrumbs::for('personal.roles.index', function (BreadcrumbTrail $trail) {
    $trail->parent('personal');
    $trail->push('Roles', route('personal.roles.index'));
});

Breadcrumbs::for('personal.roles.create', function (BreadcrumbTrail $trail) {
    $trail->parent('personal.roles.index');
    $trail->push('Nuevo rol', route('personal.roles.create'));
});

Breadcrumbs::for('personal.roles.edit', function (BreadcrumbTrail $trail, \Spatie\Permission\Models\Role $role) {
    $trail->parent('personal.roles.index');
    $trail->push($role->name, route('personal.roles.edit', $role));
});

// ── Personal › Usuarios ───────────────────────────────────────────────────

Breadcrumbs::for('personal.usuarios.index', function (BreadcrumbTrail $trail) {
    $trail->parent('personal');
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
