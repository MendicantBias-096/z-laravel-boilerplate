<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Valida el Número de Seguridad Social (NSS) del IMSS: 11 dígitos.
 *
 * 2 dígitos subdelegación (01-99) + 2 año de inscripción + 2 año de
 * nacimiento + 4 clave del trabajador + 1 dígito verificador.
 */
class NSSFormat implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $pattern = '/^(0[1-9]|[1-9][0-9])\d{9}$/';

        if (! is_string($value) || ! preg_match($pattern, $value)) {
            $fail('validation.nss')->translate();
        }
    }
}
