<?php

namespace Tests\Feature\Personal;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_roles_index(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->get(route('personal.roles.index'))
            ->assertStatus(200);
    }

    public function test_admin_can_access_create_role_page(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->get(route('personal.roles.create'))
            ->assertStatus(200);
    }

    public function test_non_admin_cannot_access_create_role_page(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)
            ->get(route('personal.roles.create'))
            ->assertForbidden();
    }

    public function test_admin_can_access_edit_role_page(): void
    {
        $admin = $this->createAdmin();
        $role  = Role::create(['name' => 'test-role']);

        $this->actingAs($admin)
            ->get(route('personal.roles.edit', $role))
            ->assertStatus(200);
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
