<?php

declare(strict_types=1);

namespace Tests\Feature\General;

use App\Providers\AppServiceProvider;
use RuntimeException;
use Tests\TestCase;

/**
 * `.env.example` trae `APP_DEBUG=true` porque es la plantilla de desarrollo, y
 * es la misma que copia `composer setup` y quien despliega con prisa. Este caso
 * existe porque un recordatorio en el checklist de despliegue no lo impide.
 */
class ConfiguracionDeProduccionTest extends TestCase
{
    public function test_la_aplicacion_no_arranca_con_debug_en_produccion(): void
    {
        $this->app->detectEnvironment(fn (): string => 'production');
        config(['app.debug' => true]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('APP_DEBUG=true con APP_ENV=production');

        new AppServiceProvider($this->app)->boot();
    }

    public function test_produccion_sin_debug_arranca(): void
    {
        $this->app->detectEnvironment(fn (): string => 'production');
        config(['app.debug' => false]);

        new AppServiceProvider($this->app)->boot();

        $this->assertFalse(config('app.debug'));
    }
}
