<?php

declare(strict_types=1);

namespace Tests\Feature\General;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Una cabecera que falta no rompe nada, y por eso desaparece sin que nadie lo
 * note: la app estuvo sin ninguna de las cinco hasta agosto de 2026. Estos
 * casos existen para que el próximo refactor de middleware no las pierda en
 * silencio.
 */
class CabecerasDeSeguridadTest extends TestCase
{
    use RefreshDatabase;

    public function test_toda_pagina_lleva_las_cabeceras_estaticas(): void
    {
        $this->get('/login')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_la_csp_bloquea(): void
    {
        $response = $this->get('/login');

        $response->assertHeader('Content-Security-Policy');
        $response->assertHeaderMissing('Content-Security-Policy-Report-Only');

        $this->assertStringContainsString(
            "frame-ancestors 'none'",
            (string) $response->headers->get('Content-Security-Policy'),
        );
    }

    /**
     * La válvula que usa un proyecto hijo mientras mide un origen nuevo. Sin
     * ella, la única forma de averiguar qué falta es romper la interfaz.
     */
    public function test_report_only_reporta_sin_bloquear(): void
    {
        config(['security.csp.report_only' => true]);

        $this->get('/login')
            ->assertHeader('Content-Security-Policy-Report-Only')
            ->assertHeaderMissing('Content-Security-Policy');
    }

    /**
     * Los dos orígenes salieron de recoger los reportes del navegador, no de
     * suponerlos. Ahora que la política bloquea, quitarlos deja la interfaz sin
     * la tipografía y sin los emojis, y en producción no hay consola que leer.
     */
    public function test_la_csp_admite_los_origenes_que_las_vistas_usan(): void
    {
        $policy = (string) $this->get('/login')
            ->headers->get('Content-Security-Policy');

        $this->assertStringContainsString("style-src 'self' 'unsafe-inline' https://fonts.bunny.net", $policy);
        $this->assertStringContainsString('font-src \'self\' data: https://fonts.bunny.net', $policy);
        $this->assertStringContainsString('img-src \'self\' data: blob: https://fonts.gstatic.com', $policy);
    }

    /**
     * Mandar HSTS por `http://` no protege nada y clava el dominio a https en
     * el navegador del desarrollador durante un año.
     */
    public function test_el_hsts_no_viaja_sobre_una_conexion_insegura(): void
    {
        $this->get('http://localhost/login')
            ->assertHeaderMissing('Strict-Transport-Security');
    }

    public function test_el_hsts_viaja_sobre_una_conexion_segura(): void
    {
        $this->get('https://localhost/login')
            ->assertHeader(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains',
            );
    }
}
