<?php

namespace App\Modules\Platform\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Exige una contraseña de al menos 8 caracteres, con una letra mayúscula
 * y un número. Complementa (no reemplaza) las reglas de Fortify.
 */
class PasswordStrength implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)
            || mb_strlen($value) < 8
            || ! preg_match('/[A-Z]/', $value)
            || ! preg_match('/\d/', $value)
        ) {
            $fail('validation.password_strength')->translate();
        }
    }
}
