<?php

declare(strict_types=1);

namespace App\Modules\Access\Tests\Feature\Api;

use App\Modules\Access\Models\Profile;
use App\Modules\Access\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * `/api/user` devolvía el modelo entero, con `$hidden` como única defensa.
 *
 * La diferencia entre esconder y publicar no es de estilo: con `$hidden`, una
 * columna nueva aparece en la respuesta el día que se crea la migración, y
 * nadie lo revisa. Estos casos fijan la lista corta.
 */
class UsuarioApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_devuelve_solo_los_campos_publicados(): void
    {
        $user = $this->usuarioConPerfil();
        Sanctum::actingAs($user);

        $respuesta = $this->getJson('/api/user')->assertOk();

        $this->assertSame(
            ['id', 'username', 'email', 'first_name', 'last_name', 'roles', 'created_at'],
            array_keys($respuesta->json('data')),
        );
    }

    public function test_no_publica_el_estado_interno_de_la_cuenta(): void
    {
        Sanctum::actingAs($this->usuarioConPerfil());

        $respuesta = $this->getJson('/api/user')->assertOk();

        foreach (['is_active', 'is_protected', 'deleted_at', 'password', 'two_factor_secret'] as $campo) {
            $respuesta->assertJsonMissingPath("data.{$campo}");
        }
    }

    public function test_un_invitado_no_alcanza_el_endpoint(): void
    {
        $this->getJson('/api/user')->assertUnauthorized();
    }

    private function usuarioConPerfil(): User
    {
        $this->seedRoles();

        $user = User::factory()->create();
        $user->assignRole('user');

        Profile::create([
            'user_id' => $user->id,
            'first_name' => 'Nombre',
            'last_name' => 'Apellido',
        ]);

        return $user;
    }
}
