<?php

namespace App\Enums;

use App\Enums\Concerns\ExtendedEnum;

enum Roles: string
{
    use ExtendedEnum;

    case ADMIN = 'admin';
    case USER  = 'user';
}
