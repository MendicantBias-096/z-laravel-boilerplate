<?php

namespace Tests\Unit;

use App\Rules\CurrencyFormat;
use App\Rules\NSSFormat;
use App\Rules\PasswordStrength;
use App\Rules\RFCFormat;
use Illuminate\Contracts\Validation\ValidationRule;
use PHPUnit\Framework\TestCase;

class RulesValidationTest extends TestCase
{
    /** Corre la regla sin bootstrapear Laravel; devuelve true si pasa. */
    private function passes(ValidationRule $rule, mixed $value): bool
    {
        $failed = false;

        $fail = function () use (&$failed) {
            $failed = true;

            // Sustituye la PotentiallyTranslatedString real; aquí solo
            // hace falta ->translate().
            return new class
            {
                public function translate(): void {}
            };
        };

        $rule->validate('field', $value, $fail);

        return ! $failed;
    }

    public function test_rfc_format(): void
    {
        // Persona física lleva 4 letras iniciales; moral, 3.
        $this->assertTrue($this->passes(new RFCFormat, 'GODE561231GR8'));  // física
        $this->assertTrue($this->passes(new RFCFormat, 'ABC680524P76'));   // moral
        $this->assertTrue($this->passes(new RFCFormat, 'gode561231gr8'));  // minúsculas
        $this->assertFalse($this->passes(new RFCFormat, 'GODE561331GR8')); // mes 13
        $this->assertFalse($this->passes(new RFCFormat, 'GO561231GR8'));   // 2 letras
        $this->assertFalse($this->passes(new RFCFormat, ''));
    }

    public function test_nss_format(): void
    {
        $this->assertTrue($this->passes(new NSSFormat, '12345678901'));
        $this->assertFalse($this->passes(new NSSFormat, '00345678901'));  // subdeleg. 00
        $this->assertFalse($this->passes(new NSSFormat, '1234567890'));    // 10 dígitos
        $this->assertFalse($this->passes(new NSSFormat, '1234567890A'));   // no numérico
    }

    public function test_currency_format(): void
    {
        $this->assertTrue($this->passes(new CurrencyFormat, '1234.56'));
        $this->assertTrue($this->passes(new CurrencyFormat, '1000'));
        $this->assertFalse($this->passes(new CurrencyFormat, '1234.567'));  // 3 decimales
        $this->assertFalse($this->passes(new CurrencyFormat, '12.3.4'));
        $this->assertFalse($this->passes(new CurrencyFormat, 'abc'));
    }

    public function test_password_strength(): void
    {
        $this->assertTrue($this->passes(new PasswordStrength, 'Segura123'));
        $this->assertFalse($this->passes(new PasswordStrength, 'corta1A'));    // < 8
        $this->assertFalse($this->passes(new PasswordStrength, 'sinmayus1'));  // may.
        $this->assertFalse($this->passes(new PasswordStrength, 'SinNumero'));  // núm.
    }
}
