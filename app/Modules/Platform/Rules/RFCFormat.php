<?php

namespace App\Modules\Platform\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Valida el formato de un RFC mexicano (personas físicas y morales).
 *
 * Estructura: 3-4 letras (apellidos / razón social) + 6 dígitos de fecha
 * (AAMMDD) + 3 caracteres de homoclave asignada por el SAT.
 */
class RFCFormat implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $pattern = '/^[A-ZÑ&]{3,4}\d{2}(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])[A-Z\d]{3}$/u';

        if (! is_string($value) || ! preg_match($pattern, mb_strtoupper($value))) {
            $fail('validation.rfc')->translate();
        }
    }
}
