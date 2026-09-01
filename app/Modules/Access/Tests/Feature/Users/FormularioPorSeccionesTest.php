<?php

declare(strict_types=1);

namespace App\Modules\Access\Tests\Feature\Users;

use App\Modules\Access\Livewire\Users\Form;
use App\Modules\Access\Models\User;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Un formulario partido en secciones esconde sus propios fallos.
 *
 * Si el correo está repetido y esa pestaña no está abierta, el guardado no
 * hace nada y no se ve por qué: el mensaje existe, pero en una sección que
 * nadie está mirando. De ahí el salto automático, que es lo que estos casos
 * cubren junto con la guarda de la sección.
 */
class FormularioPorSeccionesTest extends TestCase
{
    use RefreshDatabase;

    public function test_se_puede_cambiar_de_seccion(): void
    {
        Livewire::actingAs($this->createAdmin())
            ->test(Form::class)
            ->assertSet('section', 'identity')
            ->call('goTo', 'access')
            ->assertSet('section', 'access');
    }

    /**
     * La clave llega del navegador. Sin la guarda, `section` acepta cualquier
     * cadena y el chasis dibuja una caja vacía sin decir por qué.
     */
    public function test_una_seccion_inventada_no_cambia_nada(): void
    {
        Livewire::actingAs($this->createAdmin())
            ->test(Form::class)
            ->call('goTo', 'no-existe')
            ->assertSet('section', 'identity');
    }

    public function test_guardar_lleva_a_la_seccion_del_primer_error(): void
    {
        $admin = $this->createAdmin();
        $ocupado = User::factory()->create(['email' => 'ocupado@example.com']);

        Livewire::actingAs($admin)
            ->test(Form::class)
            ->set('form.first_name', 'Nombre')
            ->set('form.last_name', 'Apellido')
            ->set('form.username', 'nuevo')
            ->set('form.password', 'password123')
            ->set('form.password_confirmation', 'password123')
            // El correo vive en «Cuenta», y el formulario está abierto en
            // «Identidad».
            ->set('form.email', $ocupado->email)
            ->call('save')
            ->assertHasErrors('form.email')
            ->assertSet('section', 'account');
    }

    public function test_el_badge_de_accesos_cuenta_los_permisos_concedidos(): void
    {
        $componente = Livewire::actingAs($this->createAdmin())->test(Form::class);

        $secciones = $componente->instance()->sections();
        $acceso = collect($secciones)->firstWhere('key', 'access');

        $this->assertStringStartsWith('0/', (string) $acceso['badge']);

        $componente->set('permissionList', ['access.users.view']);

        $acceso = collect($componente->instance()->sections())->firstWhere('key', 'access');
        $this->assertStringStartsWith('1/', (string) $acceso['badge']);
    }

    /**
     * El aviso que hace útil el menú: un formulario partido esconde lo que
     * falta por guardar en las secciones que no se ven.
     */
    public function test_el_rail_marca_la_seccion_con_cambios_sin_guardar(): void
    {
        $usuario = User::factory()->create();

        $componente = Livewire::actingAs($this->createAdmin())
            ->test(Form::class, ['record' => $usuario]);

        $this->assertSame(
            ['identity' => false, 'account' => false, 'access' => false],
            $componente->instance()->dirtySections(),
        );

        // El correo vive en «Cuenta», y solo esa sección debe marcarse.
        $componente->set('form.email', 'otro@example.com');

        $sucias = $componente->instance()->dirtySections();

        $this->assertTrue($sucias['account']);
        $this->assertFalse($sucias['identity']);
        $this->assertFalse($sucias['access']);
    }

    /**
     * En un alta no hay con qué comparar, y marcar todo desde el primer
     * carácter convierte el aviso en ruido.
     */
    public function test_un_alta_no_marca_nada_como_sin_guardar(): void
    {
        $componente = Livewire::actingAs($this->createAdmin())
            ->test(Form::class)
            ->set('form.email', 'nuevo@example.com');

        $this->assertSame([], $componente->instance()->dirtySections());
    }

    /**
     * El pie del chasis es hijo directo del formulario, no un nodo suelto
     * dentro del cuerpo que se desplaza.
     *
     * Se comprueba con un parser y no comparando posiciones de texto: un
     * `<div>` de más cierra el `<form>` antes de tiempo, y entonces el pie
     * sigue apareciendo después en el HTML —así que el orden no delata nada—
     * pero cuelga fuera del formulario. El navegador repara el árbol igual que
     * `DOMDocument`, así que lo que este caso ve es lo que se ve en pantalla:
     * los botones «Cancelar» y «Guardar» dejan de estar anclados.
     */
    public function test_el_pie_es_hijo_del_formulario_en_todas_las_secciones(): void
    {
        $componente = Livewire::actingAs($this->createAdmin())->test(Form::class);

        foreach (['identity', 'account', 'access'] as $seccion) {
            $html = $componente->call('goTo', $seccion)->html();

            $dom = new DOMDocument;
            @$dom->loadHTML('<?xml encoding="UTF-8">'.$html, LIBXML_NOERROR);

            $pie = new DOMXPath($dom)->query("//form/div[contains(@class, 'h-14')]");

            $this->assertNotFalse($pie);
            $this->assertSame(
                1,
                $pie->length,
                "En la sección «{$seccion}» el pie no cuelga del formulario: "
                .'un `<div>` sin cerrar lo dejó fuera y los botones ya no están anclados.',
            );
        }
    }
}
