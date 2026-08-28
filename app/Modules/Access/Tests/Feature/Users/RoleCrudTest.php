<?php

namespace App\Modules\Access\Tests\Feature\Users;

use App\Modules\Access\Livewire\Roles\Form as RoleForm;
use App\Modules\Access\Livewire\Roles\Table as RoleTable;
use App\Modules\Access\Models\Role as AccessRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_roles_index(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->get(route('access.roles.index'))
            ->assertStatus(200);
    }

    public function test_admin_can_access_create_role_page(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->get(route('access.roles.create'))
            ->assertStatus(200);
    }

    public function test_non_admin_cannot_access_create_role_page(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)
            ->get(route('access.roles.create'))
            ->assertForbidden();
    }

    public function test_admin_can_access_edit_role_page(): void
    {
        $admin = $this->createAdmin();
        $role = Role::create(['name' => 'test-role']);

        $this->actingAs($admin)
            ->get(route('access.roles.edit', $role))
            ->assertStatus(200);
    }

    /**
     * Renombrar la etiqueta no puede mover el identificador.
     *
     * `Gate::before` pregunta por `Roles::ADMIN` y el seeder busca por `name`:
     * si editar «Administrador» regenerase el slug, todos los administradores
     * perderían sus privilegios sin un error y el siguiente seed crearía un
     * segundo rol `admin`.
     */
    public function test_renaming_a_role_keeps_its_slug(): void
    {
        $this->actingAs($this->createAdmin());
        $role = AccessRole::create(['name' => 'almacen', 'display_name' => 'Almacén']);

        Livewire::test(RoleForm::class, ['record' => $role])
            ->set('display_name', 'Jefe de Almacén')
            ->call('save');

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'almacen',
            'display_name' => 'Jefe de Almacén',
        ]);
    }

    /** Y al crear sí se deriva del nombre visible, que es de donde sale. */
    public function test_creating_a_role_derives_the_slug_from_its_name(): void
    {
        $this->actingAs($this->createAdmin());

        Livewire::test(RoleForm::class)
            ->set('display_name', 'Jefe de Almacén')
            ->call('save');

        $this->assertDatabaseHas('roles', [
            'name' => 'jefe-de-almacen',
            'display_name' => 'Jefe de Almacén',
        ]);
    }

    /**
     * Un rol de plataforma se ve, pero no se guarda.
     *
     * El `disabled` de la vista es una cortesía: lo que decide es el guard del
     * componente, porque quitar el atributo desde el navegador es trivial.
     */
    public function test_a_protected_role_cannot_be_edited(): void
    {
        $this->actingAs($this->createAdmin());
        $this->seedRoles();
        $role = AccessRole::findByName('admin');

        Livewire::test(RoleForm::class, ['record' => $role])
            ->set('display_name', 'Otro nombre')
            ->call('save');

        $this->assertDatabaseHas('roles', ['id' => $role->id, 'display_name' => 'Administrador']);
    }

    /** Ni se borra: el seeder lo recrearía en la próxima instalación. */
    public function test_a_protected_role_cannot_be_deleted(): void
    {
        $this->actingAs($this->createAdmin());
        $this->seedRoles();
        $role = AccessRole::findByName('user');

        Livewire::test(RoleTable::class)->call('delete', $role->id);

        $this->assertDatabaseHas('roles', ['id' => $role->id]);
    }

    /** Y el rol que crea el cliente no hereda la marca: manda él. */
    public function test_a_role_created_from_the_interface_is_not_protected(): void
    {
        $this->actingAs($this->createAdmin());

        Livewire::test(RoleForm::class)
            ->set('display_name', 'Jefe de Almacén')
            ->call('save');

        $this->assertDatabaseHas('roles', ['name' => 'jefe-de-almacen', 'is_protected' => false]);
    }

    public function test_role_exists_in_database_after_seeding(): void
    {
        $this->seedRoles();

        $this->assertDatabaseHas('roles', ['name' => 'admin']);
        $this->assertDatabaseHas('roles', ['name' => 'user']);
    }

    public function test_admin_role_has_expected_permissions(): void
    {
        $this->seedRoles();

        $adminRole = Role::findByName('admin');

        $this->assertTrue($adminRole->hasPermissionTo('ver usuarios'));
        $this->assertTrue($adminRole->hasPermissionTo('crear usuarios'));
        $this->assertTrue($adminRole->hasPermissionTo('editar usuarios'));
        $this->assertTrue($adminRole->hasPermissionTo('eliminar usuarios'));
        $this->assertTrue($adminRole->hasPermissionTo('ver roles'));
        $this->assertTrue($adminRole->hasPermissionTo('administrar sistema'));
    }

    public function test_user_role_has_no_permissions(): void
    {
        $this->seedRoles();

        $userRole = Role::findByName('user');

        $this->assertCount(0, $userRole->permissions);
    }
}
