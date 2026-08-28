<?php

declare(strict_types=1);

namespace App\Modules\Access\Enums;

use App\Modules\Platform\Enums\Concerns\ExtendedEnum;

enum Roles: string
{
    use ExtendedEnum;

    case ADMIN = 'admin';
    case USER = 'user';
}
