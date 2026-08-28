<?php

declare(strict_types=1);

namespace App\Modules\Access\Tests\Feature\Users;

use App\Modules\Access\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Las dos guardas de `UserPolicy::update()`, que antes eran `abort_if`.
 *
 * Corren para el admin porque ya no hay `Gate::before` que las salte: eso es
 * justo lo que estos tres casos protegen.
 */
class EditarUsuarioPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_admin_can_edit_another_user(): void
    {
        $admin = $this->createAdmin();
        $otro = User::factory()->create();

        $this->actingAs($admin)
            ->get(route('access.users.edit', $otro))
            ->assertStatus(200);
    }

    public function test_nobody_edits_themselves_from_the_admin_screen(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->get(route('access.users.edit', $admin))
            ->assertForbidden();
    }

    public function test_a_protected_user_is_not_found(): void
    {
        $admin = $this->createAdmin();
        $protegido = User::factory()->create(['is_protected' => true]);

        $this->actingAs($admin)
            ->get(route('access.users.edit', $protegido))
            ->assertNotFound();
    }
}
