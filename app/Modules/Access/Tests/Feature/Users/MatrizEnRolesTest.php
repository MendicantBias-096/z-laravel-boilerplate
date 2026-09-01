<?php

declare(strict_types=1);

namespace App\Modules\Access\Tests\Feature\Users;

use App\Modules\Access\Livewire\Roles\Form;
use App\Modules\Access\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Roles reparte exactamente los mismos permisos que Usuarios, así que usa la
 * misma matriz. Lo único suyo es que un rol de plataforma se muestra pero no
 * se edita.
 */
class MatrizEnRolesTest extends TestCase
{
    use RefreshDatabase;

    public function test_roles_usa_la_misma_matriz_que_usuarios(): void
    {
        $matriz = $this->componente()->instance()->permissionMatrix();

        $this->assertSame(['view', 'create', 'update', 'delete'], $matriz['tables']['access']['columns']);
        $this->assertArrayHasKey('configuracion', $matriz['blocks']);
    }

    public function test_la_maestra_de_columna_funciona_igual_que_en_usuarios(): void
    {
        $this->componente()
            ->call('toggleColumn', 'access', 'view')
            ->assertSet('permissionList', ['access.users.view', 'access.roles.view']);
    }

    /**
     * El seeder marca como protegidos los roles que declara el código. Dejar
     * sus controles vivos promete un cambio que el siguiente despliegue
     * revertiría.
     */
    public function test_un_rol_de_plataforma_muestra_la_matriz_pero_desactivada(): void
    {
        $this->seedRoles();
        $protegido = Role::where('name', 'admin')->firstOrFail();

        $html = Livewire::actingAs($this->createAdmin())
            ->test(Form::class, ['record' => $protegido])
            ->call('goTo', 'permissions')
            ->html();

        $dom = new \DOMDocument;
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$html, LIBXML_NOERROR);

        $casillas = new \DOMXPath($dom)->query('//input[@type="checkbox"]');
        $desactivadas = new \DOMXPath($dom)->query('//input[@type="checkbox"][@disabled]');

        $this->assertGreaterThan(0, $casillas->length, 'La matriz no se muestra.');
        $this->assertSame($casillas->length, $desactivadas->length, 'Alguna casilla sigue viva en un rol protegido.');
    }

    public function test_un_rol_normal_tiene_la_matriz_viva(): void
    {
        $html = $this->componente()->call('goTo', 'permissions')->html();

        $dom = new \DOMDocument;
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$html, LIBXML_NOERROR);

        $this->assertSame(0, new \DOMXPath($dom)->query('//input[@type="checkbox"][@disabled]')->length);
    }

    private function componente(): Testable
    {
        return Livewire::actingAs($this->createAdmin())->test(Form::class);
    }
}
