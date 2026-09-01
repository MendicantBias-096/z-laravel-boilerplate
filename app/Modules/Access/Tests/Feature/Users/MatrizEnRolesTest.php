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
 * misma matriz. Dos cosas son suyas: la ficha va en una sola pantalla —un rol
 * es un nombre y una plantilla de permisos, y un menú de una entrada es un
 * menú de mentira— y un rol de plataforma se muestra pero no se edita.
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
        $html = $this->componente()->html();

        $dom = new \DOMDocument;
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$html, LIBXML_NOERROR);

        $this->assertSame(0, new \DOMXPath($dom)->query('//input[@type="checkbox"][@disabled]')->length);
    }

    /**
     * Una clave que no existe no falla: `__()` devuelve la clave misma, y en
     * pantalla se lee «platform::app.role_btn_create» donde debería ir un
     * título. Se coló una en la ficha de rol y solo se vio mirando la captura.
     */
    public function test_no_queda_ninguna_clave_de_traduccion_sin_resolver(): void
    {
        $html = $this->componente()->html();

        $this->assertDoesNotMatchRegularExpression(
            '/(platform|access)::[a-z_]+\.[a-z_]+/',
            strip_tags($html),
            'Hay una clave de traducción sin definir en la ficha de rol.',
        );
    }

    private function componente(): Testable
    {
        return Livewire::actingAs($this->createAdmin())->test(Form::class);
    }
}
