<?php

namespace App\Modules\Platform\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'platform_settings';

    protected $fillable = ['key', 'value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $cached = cache()->rememberForever("setting:{$key}", fn () => static::where('key', $key)->value('value'));

        return $cached ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        cache()->forget("setting:{$key}");
    }
}
