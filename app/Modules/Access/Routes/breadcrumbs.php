<?php

declare(strict_types=1);

use App\Modules\Access\Models\User;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use Spatie\Permission\Models\Role;

// El nodo raíz no tiene URL: es solo la etiqueta del grupo en la navegación,
// y lleva el nombre que ve un humano, no el del módulo.
Breadcrumbs::for('access', function (BreadcrumbTrail $trail): void {
    $trail->push(__('platform::menu.access'));
});

Breadcrumbs::for('access.roles.index', function (BreadcrumbTrail $trail): void {
    $trail->parent('access');
    $trail->push(__('access::roles.title'), route('access.roles.index'));
});

Breadcrumbs::for('access.roles.create', function (BreadcrumbTrail $trail): void {
    $trail->parent('access.roles.index');
    $trail->push(__('access::roles.create'), route('access.roles.create'));
});

Breadcrumbs::for('access.roles.edit', function (BreadcrumbTrail $trail, Role $role): void {
    $trail->parent('access.roles.index');
    $trail->push($role->name, route('access.roles.edit', $role));
});

Breadcrumbs::for('access.users.index', function (BreadcrumbTrail $trail): void {
    $trail->parent('access');
    $trail->push(__('access::roles.users'), route('access.users.index'));
});

Breadcrumbs::for('access.users.create', function (BreadcrumbTrail $trail): void {
    $trail->parent('access.users.index');
    $trail->push(__('access::roles.new_user'), route('access.users.create'));
});

Breadcrumbs::for('access.users.edit', function (BreadcrumbTrail $trail, User $user): void {
    $trail->parent('access.users.index');
    $trail->push($user->name, route('access.users.edit', $user));
});
