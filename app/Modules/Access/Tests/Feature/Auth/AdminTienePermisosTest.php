<?php

declare(strict_types=1);

namespace App\Modules\Access\Tests\Feature\Auth;

use App\Modules\Access\Enums\Roles;
use App\Modules\Access\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Lo que sostiene al admin ahora que no hay `Gate::before`.
 *
 * Antes el rol pasaba cualquier comprobación por un atajo en
 * `AppServiceProvider` que cortaba antes de toda Policy. Quitarlo deja al
 * admin dependiendo de tener los permisos de verdad, así que la invariante
 * pasa a ser esta: todo permiso que exista está asignado al rol admin. Un
 * módulo nuevo que cree permisos y no se los dé pone este test en rojo, que
 * es exactamente el fallo que antes solo veía un usuario no-admin.
 */
class AdminTienePermisosTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_admin_role_holds_every_permission(): void
    {
        $this->seedRoles();

        $todos = Permission::pluck('name');
        $admin = Role::findByName(Roles::ADMIN->value)->permissions->pluck('name');

        $this->assertNotEmpty($todos, 'El seeder debe crear permisos.');
        $this->assertEmpty(
            $todos->diff($admin)->all(),
            'Sin Gate::before, un permiso que el admin no tenga es acceso que pierde: '
                .$todos->diff($admin)->implode(', ')
        );
    }

    public function test_a_user_without_permission_is_denied(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)
            ->get(route('access.users.index'))
            ->assertForbidden();
    }
}
