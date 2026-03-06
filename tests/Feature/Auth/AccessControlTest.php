<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_on_protected_routes(): void
    {
        $protectedRoutes = [
            route('dashboard'),
            route('settings'),
        ];

        foreach ($protectedRoutes as $route) {
            $this->get($route)->assertRedirect(route('login'));
        }
    }

    public function test_user_without_permission_cannot_access_roles_list(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)
            ->get(route('personal.roles.index'))
            ->assertForbidden();
    }

    public function test_admin_can_access_roles_list(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->get(route('personal.roles.index'))
            ->assertStatus(200);
    }

    public function test_user_without_permission_cannot_access_users_list(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)
            ->get(route('personal.usuarios.index'))
            ->assertForbidden();
    }

    public function test_admin_can_access_users_list(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->get(route('personal.usuarios.index'))
            ->assertStatus(200);
    }

    public function test_admin_can_access_settings(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->get(route('settings'))
            ->assertStatus(200);
    }

    public function test_regular_user_can_access_dashboard(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertStatus(200);
    }
}
