<?php

use App\Models\User;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use Spatie\Permission\Models\Role;

// ── General › Notificaciones ──────────────────────────────────────────────

Breadcrumbs::for('general.notifications.index', function (BreadcrumbTrail $trail): void {
    $trail->push(__('notifications.title'), route('general.notifications.index'));
});

Breadcrumbs::for('general.docs.index', function (BreadcrumbTrail $trail): void {
    $trail->push(__('docs.title'), route('general.docs.index'));
});

// ── Personal ──────────────────────────────────────────────────────────────

Breadcrumbs::for('personal', function (BreadcrumbTrail $trail): void {
    $trail->push('Personal'); // sin URL — solo etiqueta de dominio
});

// ── Personal › Roles ──────────────────────────────────────────────────────

Breadcrumbs::for('personal.roles.index', function (BreadcrumbTrail $trail): void {
    $trail->parent('personal');
    $trail->push('Roles', route('personal.roles.index'));
});

Breadcrumbs::for('personal.roles.create', function (BreadcrumbTrail $trail): void {
    $trail->parent('personal.roles.index');
    $trail->push('Nuevo rol', route('personal.roles.create'));
});

Breadcrumbs::for('personal.roles.edit', function (BreadcrumbTrail $trail, Role $role): void {
    $trail->parent('personal.roles.index');
    $trail->push($role->name, route('personal.roles.edit', $role));
});

// ── Personal › Usuarios ───────────────────────────────────────────────────

Breadcrumbs::for('personal.usuarios.index', function (BreadcrumbTrail $trail): void {
    $trail->parent('personal');
    $trail->push('Usuarios', route('personal.usuarios.index'));
});

Breadcrumbs::for('personal.usuarios.create', function (BreadcrumbTrail $trail): void {
    $trail->parent('personal.usuarios.index');
    $trail->push('Nuevo usuario', route('personal.usuarios.create'));
});

Breadcrumbs::for('personal.usuarios.edit', function (BreadcrumbTrail $trail, User $user): void {
    $trail->parent('personal.usuarios.index');
    $trail->push($user->name, route('personal.usuarios.edit', $user));
});
