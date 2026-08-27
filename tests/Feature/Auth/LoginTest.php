<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_accessible_for_guests(): void
    {
        $this->get(route('login'))->assertStatus(200);
    }

    public function test_authenticated_user_is_redirected_away_from_login(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('login'))
            ->assertRedirect(route('dashboard'));
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors();

        $this->assertGuest();
    }

    public function test_user_cannot_login_with_nonexistent_email(): void
    {
        $this->post('/login', [
            'email' => 'noexiste@example.com',
            'password' => 'password',
        ])->assertSessionHasErrors();

        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/');

        $this->assertGuest();
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_quick_login_authenticates_in_local(): void
    {
        $this->app->detectEnvironment(fn (): string => 'local');
        $user = User::factory()->create(['email' => 'admin@example.com']);

        Livewire::test(Login::class)
            ->call('quickLogin', 'admin@example.com')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    /**
     * La guarda vive en el servidor, no en el `@if` de la vista: ocultar el
     * botón no basta si alguien invoca el método desde el cliente.
     */
    public function test_quick_login_is_forbidden_outside_local(): void
    {
        $this->app->detectEnvironment(fn (): string => 'production');
        User::factory()->create(['email' => 'admin@example.com']);

        Livewire::test(Login::class)
            ->call('quickLogin', 'admin@example.com')
            ->assertForbidden();

        $this->assertGuest();
    }

    /**
     * Con la base sin sembrar el fallo era un 404 mudo. Ahora el mensaje
     * nombra el comando que lo arregla.
     */
    public function test_quick_login_explains_when_the_user_is_not_seeded(): void
    {
        $this->app->detectEnvironment(fn (): string => 'local');

        Livewire::test(Login::class)
            ->call('quickLogin', 'noexiste@example.com')
            ->assertHasErrors('email')
            ->assertNoRedirect();

        $this->assertGuest();
    }
}
