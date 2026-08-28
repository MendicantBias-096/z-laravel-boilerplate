<?php

declare(strict_types=1);

namespace App\Modules\Platform\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\DatabaseNotification as BaseDatabaseNotification;
use Illuminate\Support\Carbon;

/**
 * @property Carbon|null $created_at
 * @property Carbon|null $read_at
 */
class DatabaseNotification extends BaseDatabaseNotification
{
    use SoftDeletes;
}
