<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Crea un usuario con rol 'admin' y todos sus permisos.
     */
    protected function createAdmin(): \App\Models\User
    {
        $this->seedRoles();
        $user = \App\Models\User::factory()->create();
        $user->assignRole('admin');

        return $user;
    }

    /**
     * Crea un usuario con rol 'user' (sin permisos adicionales).
     */
    protected function createUser(): \App\Models\User
    {
        $this->seedRoles();
        $user = \App\Models\User::factory()->create();
        $user->assignRole('user');

        return $user;
    }

    protected function seedRoles(): void
    {
        (new \Database\Seeders\RolesAndPermissionsSeeder())->run();
    }
}
