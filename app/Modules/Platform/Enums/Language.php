<?php

namespace App\Modules\Platform\Enums;

enum Language: string
{
    case Spanish = 'es';
    case English = 'en';

    /**
     * Etiqueta legible para mostrar en selectores.
     */
    public function label(): string
    {
        return __("platform::enums.language.{$this->value}");
    }

    /**
     * Array de opciones para x-ts-select: [['label' => ..., 'value' => ...]].
     *
     * @return array<int, array{label: string, value: string}>
     */
    public static function options(): array
    {
        return array_map(
            fn (self $case): array => ['label' => $case->label(), 'value' => $case->value],
            self::cases()
        );
    }

    /**
     * Array de valores válidos para validación.
     *
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
