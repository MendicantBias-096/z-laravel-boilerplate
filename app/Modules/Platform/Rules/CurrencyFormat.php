<?php

namespace App\Modules\Platform\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Valida una cantidad monetaria: entero o decimal con hasta 2 decimales
 * (ej. 1234.56). Acepta hasta 15 dígitos enteros.
 */
class CurrencyFormat implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $pattern = '/^\d{1,15}(\.\d{1,2})?$/';

        if (! preg_match($pattern, (string) $value)) {
            $fail('validation.currency')->translate();
        }
    }
}
