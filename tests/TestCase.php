<?php

namespace Tests;

use App\Modules\Access\Database\Seeders\RolesAndPermissionsSeeder;
use App\Modules\Access\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\PermissionRegistrar;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Un test afirma comportamiento, no que Vite haya compilado. Sin esto,
        // `public/build` ausente hace fallar «la página carga» con el nombre
        // equivocado, y el agente que lo lee va a arreglar código sano (R56).
        $this->withoutVite();

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
