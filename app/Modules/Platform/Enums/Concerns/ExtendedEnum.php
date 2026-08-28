<?php

namespace App\Modules\Platform\Enums\Concerns;

trait ExtendedEnum
{
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function asOptions(): array
    {
        return array_map(fn ($case): array => [
            'value' => $case->value,
            'label' => $case->description(),
        ], self::cases());
    }

    public function description(): string
    {
        return __($this->value);
    }
}
