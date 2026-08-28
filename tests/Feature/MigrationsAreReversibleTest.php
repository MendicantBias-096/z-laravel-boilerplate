<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

/**
 * R37 · Toda migración sabe deshacerse, y aquí se comprueba de verdad.
 *
 * Diez líneas que cubren las migraciones presentes y las futuras. Un `down()`
 * que nunca se probó falla el día que hace falta, que suele ser de madrugada y
 * con prisa; y uno roto es peor que uno ausente, porque la ausencia al menos
 * es honesta.
 *
 * Con R34 se itera sobre una migración varias veces antes de mergearla, y
 * `migrate:rollback` es exactamente la herramienta: vale la pena que funcione.
 */
class MigrationsAreReversibleTest extends TestCase
{
    public function test_all_migrations_are_reversible(): void
    {
        $this->artisan('migrate:fresh')->assertSuccessful();
        $this->artisan('migrate:rollback', ['--step' => 100])->assertSuccessful();
        $this->artisan('migrate')->assertSuccessful();
    }
}
