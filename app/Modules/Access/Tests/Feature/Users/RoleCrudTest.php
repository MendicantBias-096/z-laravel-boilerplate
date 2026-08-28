<?php

namespace App\Modules\Access\Tests\Feature\Users;

use App\Modules\Access\Livewire\Roles\Form as RoleForm;
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
        $role = AccessRole::findByName('admin');
        $role->update(['display_name' => 'Administrador']);

        Livewire::test(RoleForm::class, ['record' => $role])
            ->set('display_name', 'Administrador General')
            ->call('save');

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'admin',
            'display_name' => 'Administrador General',
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
