<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Modules\Access\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Lo que ve alguien que clona el repositorio y corre `make setup`.
 *
 * Existe por una cicatriz concreta: al mover los seeders dentro de su módulo,
 * `DatabaseSeeder` siguió nombrándolos por namespace implícito y `db:seed`
 * quedó roto. Ningún test lo detectó porque `RefreshDatabase` llama a los
 * seeders directamente y nunca pasa por ese archivo — el camino que sí usa
 * una instalación real era justo el que no se probaba.
 */
class CleanInstallTest extends TestCase
{
    public function test_a_clean_install_seeds_roles_and_users(): void
    {
        $this->artisan('migrate:fresh')->assertSuccessful();
        $this->artisan('db:seed')->assertSuccessful();

        $this->assertDatabaseHas('users', ['email' => 'admin@example.com']);
        $this->assertTrue(User::role('admin')->exists(), 'El admin sembrado debe tener su rol.');
    }

    /**
     * El morph map guarda un alias, no el nombre de la clase.
     *
     * Sin él, mover un modelo de namespace deja las filas polimórficas
     * apuntando a una clase que ya no existe y el usuario pierde sus roles en
     * silencio: es lo que convirtió `/users` en un 403 sin explicación.
     */
    public function test_polymorphic_rows_store_an_alias_not_a_class_name(): void
    {
        $this->artisan('migrate:fresh')->assertSuccessful();
        $this->artisan('db:seed')->assertSuccessful();

        $types = DB::table('model_has_roles')->distinct()->pluck('model_type');

        $this->assertNotEmpty($types, 'Los seeders deben asignar algún rol.');

        foreach ($types as $type) {
            $this->assertStringNotContainsString('\\', (string) $type,
                "model_has_roles.model_type guarda «{$type}»: un namespace, no un alias del morph map.");
        }
    }
}
