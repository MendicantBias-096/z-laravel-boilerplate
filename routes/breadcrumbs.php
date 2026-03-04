<?php

use App\Models\User;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// ── Personal ──────────────────────────────────────────────────────────────

Breadcrumbs::for('personal', function (BreadcrumbTrail $trail) {
    $trail->push('Personal'); // sin URL — solo etiqueta de dominio
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
