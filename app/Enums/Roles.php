<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\ExtendedEnum;

enum Roles: string
{
    use ExtendedEnum;

    case ADMIN = 'admin';
    case USER = 'user';
}
