<?php

declare(strict_types=1);

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('platform.notifications.index', function (BreadcrumbTrail $trail): void {
    $trail->push(__('platform::notifications.title'), route('platform.notifications.index'));
});

Breadcrumbs::for('platform.docs.index', function (BreadcrumbTrail $trail): void {
    $trail->push(__('platform::docs.title'), route('platform.docs.index'));
});
