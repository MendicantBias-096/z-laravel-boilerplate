<?php

declare(strict_types=1);

namespace App\Modules\Access\Tests\Feature\Users;

use App\Modules\Access\Livewire\Users\Form;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * La matriz reparte los permisos sobre una rejilla de grupo → módulo → verbo.
 *
 * Lo que estos casos fijan no es el HTML sino las tres decisiones que la hacen
 * legible: qué grupos merecen tabla, qué distingue «no existe» de «no
 * concedido», y que una maestra diga la verdad sobre lo que hay debajo.
 */
class MatrizDePermisosTest extends TestCase
{
    use RefreshDatabase;

    public function test_los_grupos_con_eje_crud_van_a_tabla_y_el_resto_a_bloques(): void
    {
        $matriz = $this->componente()->instance()->permissionMatrix();

        // Accesos tiene usuarios y roles, los dos con ver/crear/editar/eliminar.
        $this->assertArrayHasKey('access', $matriz['tables']);
        $this->assertSame(['view', 'create', 'update', 'delete'], $matriz['tables']['access']['columns']);

        // Configuración tiene un único permiso que no es un verbo del eje, así
        // que forzarlo a una tabla dejaría una fila con cuatro celdas vacías.
        $this->assertArrayHasKey('configuracion', $matriz['blocks']);
    }

    /**
     * `null` en una celda significa «este permiso no existe». La vista lo pinta
     * como una raya; una casilla apagada diría «existe y no lo marcaste», que
     * es otra afirmación.
     */
    public function test_una_celda_sin_permiso_es_null_y_no_una_cadena_vacia(): void
    {
        $filas = $this->componente()->instance()->permissionMatrix()['tables']['access']['rows'];

        $this->assertSame('access.users.view', $filas['usuarios']['cells']['view']);
        $this->assertContains('access.users.restore', $filas['usuarios']['extras']);

        // Roles no tiene «restaurar», así que no aparece entre sus extras.
        $this->assertSame([], $filas['roles']['extras']);
    }

    public function test_la_maestra_de_columna_concede_el_verbo_en_todo_el_grupo(): void
    {
        $componente = $this->componente()
            ->call('toggleColumn', 'access', 'view')
            ->assertSet('permissionList', ['access.users.view', 'access.roles.view']);

        // Y vuelve a quitarlo: la maestra alterna, no solo concede.
        $componente->call('toggleColumn', 'access', 'view')
            ->assertSet('permissionList', []);
    }

    /**
     * Con solo «todos» y «no todos», 3 de 5 marcados se ve igual que 0 de 5 y
     * la casilla miente sobre lo que hay debajo.
     */
    public function test_la_maestra_distingue_los_tres_estados(): void
    {
        $componente = $this->componente();

        $this->assertSame('none', $componente->instance()->moduleState('usuarios'));

        $componente->set('permissionList', ['access.users.view']);
        $this->assertSame('some', $componente->instance()->moduleState('usuarios'));

        $componente->set('permissionList', config('roles.permissions.usuarios'));
        $this->assertSame('all', $componente->instance()->moduleState('usuarios'));
    }

    public function test_el_filtro_acota_a_los_modulos_elegidos_enteros(): void
    {
        $componente = $this->componente()->set('moduleFilter', ['usuarios']);

        $grupos = $componente->instance()->permissionsByGroup();

        $this->assertSame(['usuarios'], array_keys($grupos['access']));
        $this->assertArrayNotHasKey('configuracion', $grupos);
    }

    /**
     * El select manda `null` al vaciarse. Con el tipo estricto `array`,
     * Livewire deja la propiedad sin inicializar y la siguiente lectura muere
     * con un mensaje que apunta al sitio equivocado.
     */
    public function test_vaciar_el_filtro_no_rompe_la_matriz(): void
    {
        $componente = $this->componente()
            ->set('moduleFilter', ['usuarios'])
            ->set('moduleFilter', null);

        $this->assertNotSame([], $componente->instance()->permissionsByGroup());
    }

    /**
     * Alternar una maestra voltea varias casillas y el lector solo anuncia
     * aquella donde está el foco: sin la región viva, quien no ve la pantalla
     * oye «marcada» y no sabe qué acaba de conceder.
     */
    public function test_alternar_una_maestra_deja_dicho_que_cambio(): void
    {
        $componente = $this->componente()->call('toggleModule', 'usuarios');

        $anuncio = $componente->instance()->permissionAnnouncement;

        $this->assertStringContainsString('5', $anuncio);
        $this->assertStringContainsString(__('access::roles.modules.usuarios'), $anuncio);
    }

    private function componente(): Testable
    {
        return Livewire::actingAs($this->createAdmin())->test(Form::class);
    }
}
