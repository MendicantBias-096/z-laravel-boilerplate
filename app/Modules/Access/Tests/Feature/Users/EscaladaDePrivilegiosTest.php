<?php

declare(strict_types=1);

namespace App\Modules\Access\Tests\Feature\Users;

use App\Modules\Access\Livewire\Roles\Form as RoleForm;
use App\Modules\Access\Livewire\Users\Form as UserForm;
use App\Modules\Access\Models\Role;
use App\Modules\Access\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Las guardas de ruta no protegen a un componente Livewire.
 *
 * `permission:` y `can:` corren sobre `/users/create` y `/users/{user}/edit`,
 * pero una acción de Livewire viaja a `/livewire/update` y no atraviesa
 * ninguna de las dos. Antes de estos casos se podía abrir el alta con
 * `access.users.create`, mandar el id de otro usuario y sobrescribirlo, o
 * firmarse un permiso que no se tenía —y el suite seguía verde.
 *
 * Cada test cubre una mitad distinta: quitar el `#[Locked]`, el `authorize()`
 * o el filtro de permisos deja uno de ellos en rojo.
 */
class EscaladaDePrivilegiosTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_id_del_formulario_no_se_puede_inyectar_desde_el_cliente(): void
    {
        $actor = $this->userWithPermissions(['access.users.create']);
        $victima = User::factory()->create();

        $this->expectException(CannotUpdateLockedPropertyException::class);

        Livewire::actingAs($actor)
            ->test(UserForm::class)
            ->set('form.id', $victima->id);
    }

    public function test_el_registro_montado_no_se_puede_cambiar_desde_el_cliente(): void
    {
        $actor = $this->userWithPermissions(['access.users.create']);
        $victima = User::factory()->create();

        $this->expectException(CannotUpdateLockedPropertyException::class);

        Livewire::actingAs($actor)
            ->test(UserForm::class)
            ->set('record', $victima);
    }

    public function test_guardar_un_usuario_exige_el_permiso_de_alta(): void
    {
        $actor = $this->createUser();

        Livewire::actingAs($actor)
            ->test(UserForm::class)
            ->call('save')
            ->assertForbidden();

        $this->assertDatabaseCount('users', 1);
    }

    public function test_no_se_conceden_permisos_que_el_actor_no_tiene(): void
    {
        $actor = $this->userWithPermissions(['access.users.create']);

        Livewire::actingAs($actor)
            ->test(UserForm::class)
            ->set('form.username', 'nuevo')
            ->set('form.email', 'nuevo@example.com')
            ->set('form.first_name', 'Nuevo')
            ->set('form.last_name', 'Usuario')
            ->set('form.password', 'password123')
            ->set('form.password_confirmation', 'password123')
            ->set('permissionList', ['platform.settings.manage'])
            ->call('save')
            ->assertHasErrors('permissionList.0');

        $this->assertDatabaseMissing('users', ['email' => 'nuevo@example.com']);
    }

    public function test_no_se_asigna_un_rol_mas_privilegiado_que_el_actor(): void
    {
        $actor = $this->userWithPermissions(['access.users.create']);

        Livewire::actingAs($actor)
            ->test(UserForm::class)
            ->set('form.username', 'nuevo')
            ->set('form.email', 'nuevo@example.com')
            ->set('form.first_name', 'Nuevo')
            ->set('form.last_name', 'Usuario')
            ->set('form.password', 'password123')
            ->set('form.password_confirmation', 'password123')
            ->set('form.role', 'admin')
            ->call('save')
            ->assertHasErrors('form.role');

        $this->assertDatabaseMissing('users', ['email' => 'nuevo@example.com']);
    }

    public function test_el_admin_sigue_creando_usuarios_con_permisos(): void
    {
        Notification::fake();
        $admin = $this->createAdmin();

        Livewire::actingAs($admin)
            ->test(UserForm::class)
            ->set('form.username', 'nuevo')
            ->set('form.email', 'nuevo@example.com')
            ->set('form.first_name', 'Nuevo')
            ->set('form.last_name', 'Usuario')
            ->set('form.password', 'password123')
            ->set('form.password_confirmation', 'password123')
            ->set('permissionList', ['access.users.view'])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', ['email' => 'nuevo@example.com']);
    }

    public function test_guardar_un_rol_exige_el_permiso_de_alta(): void
    {
        $actor = $this->createUser();

        Livewire::actingAs($actor)
            ->test(RoleForm::class)
            ->call('save')
            ->assertForbidden();
    }

    public function test_un_rol_no_se_llena_con_permisos_que_el_actor_no_tiene(): void
    {
        $actor = $this->userWithPermissions(['access.roles.create']);

        Livewire::actingAs($actor)
            ->test(RoleForm::class)
            ->set('display_name', 'Suplantador')
            ->set('name', 'suplantador')
            ->set('permissionList', ['platform.settings.manage'])
            ->call('save')
            ->assertHasErrors('permissionList.0');

        $this->assertDatabaseMissing('roles', ['name' => 'suplantador']);
    }

    /**
     * El caso positivo, que es lo que evita que la regla se pase de estricta:
     * repartir lo que uno tiene es exactamente para lo que existe delegar.
     */
    public function test_si_se_concede_lo_que_el_actor_si_tiene(): void
    {
        Notification::fake();
        $actor = $this->userWithPermissions(['access.users.create', 'access.users.view']);

        Livewire::actingAs($actor)
            ->test(UserForm::class)
            ->set('form.username', 'delegado')
            ->set('form.email', 'delegado@example.com')
            ->set('form.first_name', 'Nombre')
            ->set('form.last_name', 'Apellido')
            ->set('form.password', 'password123')
            ->set('form.password_confirmation', 'password123')
            ->set('permissionList', ['access.users.view'])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', ['email' => 'delegado@example.com']);
    }

    /**
     * Lo que hace valiosa a la regla frente a una lista de «quién otorga qué»:
     * el rol se crea después de escribirla y queda cubierto igual, porque la
     * respuesta sale de sus permisos y no de una tabla que mantener a mano.
     */
    public function test_un_rol_creado_despues_se_juzga_por_sus_permisos(): void
    {
        $actor = $this->userWithPermissions(['access.users.create']);

        $supervisor = Role::create(['name' => 'supervisor', 'display_name' => 'Supervisor']);
        $supervisor->givePermissionTo('platform.settings.manage');

        Livewire::actingAs($actor)
            ->test(UserForm::class)
            ->set('form.username', 'nuevo')
            ->set('form.email', 'nuevo@example.com')
            ->set('form.first_name', 'Nombre')
            ->set('form.last_name', 'Apellido')
            ->set('form.password', 'password123')
            ->set('form.password_confirmation', 'password123')
            ->set('form.role', 'supervisor')
            ->call('save')
            ->assertHasErrors('form.role');

        $this->assertDatabaseMissing('users', ['email' => 'nuevo@example.com']);
    }

    /**
     * La misma puerta, la otra pantalla: un rol es un paquete de permisos, así
     * que llenarlo con lo que uno no tiene es la misma escalada por otro sitio.
     */
    public function test_el_error_dice_que_permiso_no_se_puede_conceder(): void
    {
        $actor = $this->userWithPermissions(['access.roles.create']);

        $componente = Livewire::actingAs($actor)
            ->test(RoleForm::class)
            ->set('display_name', 'Inventado')
            ->set('name', 'inventado')
            ->set('permissionList', ['platform.settings.manage'])
            ->call('save')
            ->assertHasErrors('permissionList.0');

        // El mensaje nombra el permiso. `Rule::in` decía «el valor
        // seleccionado no es válido», que no explica nada.
        $this->assertStringContainsString(
            'platform.settings.manage',
            (string) $componente->instance()->getErrorBag()->first('permissionList.0'),
        );
    }

    /**
     * @param  list<string>  $permissions
     */
    private function userWithPermissions(array $permissions): User
    {
        $this->seedRoles();

        $user = User::factory()->create();
        $user->givePermissionTo($permissions);

        return $user;
    }
}
