<?php

namespace App\Modules\Access\Tests\Feature\Users;

use App\Modules\Access\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_users_index(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->get(route('access.users.index'))
            ->assertStatus(200);
    }

    public function test_admin_can_access_create_user_page(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->get(route('access.users.create'))
            ->assertStatus(200);
    }

    public function test_admin_can_access_edit_user_page(): void
    {
        $admin = $this->createAdmin();
        $target = $this->createUser();

        $this->actingAs($admin)
            ->get(route('access.users.edit', $target))
            ->assertStatus(200);
    }

    public function test_user_cannot_edit_themselves(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->get(route('access.users.edit', $admin))
            ->assertForbidden();
    }

    public function test_user_factory_creates_profile(): void
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->profile);
        $this->assertNotEmpty($user->profile->first_name);
        $this->assertNotEmpty($user->profile->last_name);
    }

    public function test_soft_deleted_user_is_not_accessible_via_login(): void
    {
        $user = User::factory()->create();
        $user->delete();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSessionHasErrors();

        $this->assertGuest();
    }

    public function test_user_exists_in_database_after_factory_creation(): void
    {
        User::factory()->create([
            'username' => 'testuser',
            'email' => 'test@example.com',
        ]);

        $this->assertDatabaseHas('users', [
            'username' => 'testuser',
            'email' => 'test@example.com',
        ]);
    }
}
