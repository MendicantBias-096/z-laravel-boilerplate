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

    public function test_la_csp_arranca_reportando_y_no_bloqueando(): void
    {
        $response = $this->get('/login');

        $response->assertHeader('Content-Security-Policy-Report-Only');
        $response->assertHeaderMissing('Content-Security-Policy');

        $this->assertStringContainsString(
            "frame-ancestors 'none'",
            (string) $response->headers->get('Content-Security-Policy-Report-Only'),
        );
    }

    public function test_la_csp_bloquea_cuando_se_desactiva_report_only(): void
    {
        config(['security.csp.report_only' => false]);

        $this->get('/login')
            ->assertHeader('Content-Security-Policy')
            ->assertHeaderMissing('Content-Security-Policy-Report-Only');
    }

    /**
     * Los dos orígenes salieron de recoger los reportes del navegador en
     * `/login`, no de suponerlos. Quitarlos deja la interfaz sin la tipografía
     * y sin los emojis el día que la CSP pase a bloquear, que es cuando ya no
     * se puede averiguar leyendo la consola.
     */
    public function test_la_csp_admite_los_origenes_que_las_vistas_usan(): void
    {
        $policy = (string) $this->get('/login')
            ->headers->get('Content-Security-Policy-Report-Only');

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
