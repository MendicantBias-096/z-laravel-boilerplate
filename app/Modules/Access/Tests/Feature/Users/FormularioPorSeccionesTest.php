<?php

declare(strict_types=1);

namespace App\Modules\Access\Tests\Feature\Users;

use App\Modules\Access\Livewire\Users\Form;
use App\Modules\Access\Models\User;
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
}
