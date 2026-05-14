<?php

namespace Tests\Feature\Auth;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_page_is_accessible_for_guests(): void
    {
        $this->get(route('register'))->assertStatus(200);
    }

    public function test_user_can_register_with_valid_data(): void
    {
        $this->seedRoles();

        $this->post('/register', [
            'first_name' => 'Nuevo',
            'last_name' => 'Usuario',
            'username' => 'nuevo_usuario',
            'email' => 'nuevo@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect();

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'nuevo@example.com', 'username' => 'nuevo_usuario']);
    }

    public function test_registration_creates_a_profile_automatically(): void
    {
        $this->seedRoles();

        $this->post('/register', [
            'first_name' => 'Usuario',
            'last_name' => 'Perfil',
            'username' => 'usuario_con_perfil',
            'email' => 'perfil@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'perfil@example.com')->first();

        $this->assertNotNull($user);
        $this->assertInstanceOf(Profile::class, $user->profile);
    }

    public function test_registration_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'existente@example.com']);

        $this->post('/register', [
            'username' => 'otro_usuario',
            'email' => 'existente@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_registration_fails_with_duplicate_username(): void
    {
        User::factory()->create(['username' => 'usuario_existente']);

        $this->post('/register', [
            'username' => 'usuario_existente',
            'email' => 'diferente@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_registration_fails_when_passwords_do_not_match(): void
    {
        $this->post('/register', [
            'username' => 'usuario',
            'email' => 'user@example.com',
            'password' => 'password123',
            'password_confirmation' => 'diferente456',
        ])->assertSessionHasErrors('password');

        $this->assertGuest();
    }

    public function test_authenticated_user_cannot_access_register_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('register'))
            ->assertRedirect(route('dashboard'));
    }
}
