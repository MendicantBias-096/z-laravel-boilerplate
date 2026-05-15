<?php

namespace Tests;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\PermissionRegistrar;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Crea un usuario con rol 'admin' y todos sus permisos.
     */
    protected function createAdmin(): User
    {
        $this->seedRoles();
        $user = User::factory()->create();
        $user->assignRole('admin');

        return $user;
    }

    /**
     * Crea un usuario con rol 'user' (sin permisos adicionales).
     */
    protected function createUser(): User
    {
        $this->seedRoles();
        $user = User::factory()->create();
        $user->assignRole('user');

        return $user;
    }

    protected function seedRoles(): void
    {
        new RolesAndPermissionsSeeder()->run();
    }
}
