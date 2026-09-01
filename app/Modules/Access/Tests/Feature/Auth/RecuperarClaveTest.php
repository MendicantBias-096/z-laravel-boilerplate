<?php

declare(strict_types=1);

namespace App\Modules\Access\Tests\Feature\Auth;

use App\Modules\Access\Livewire\Auth\ForgotPassword;
use App\Modules\Access\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Lo que la pantalla de recuperación no debe contar.
 *
 * Son dos fugas distintas del mismo flujo: una le dice a un desconocido qué
 * correos tienen cuenta, y la otra escribe el token de un solo uso en
 * `storage/logs`, donde sobrevive a la caducidad del enlace.
 */
class RecuperarClaveTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_correo_desconocido_no_se_distingue_de_uno_registrado(): void
    {
        $registrado = User::factory()->create();

        Livewire::test(ForgotPassword::class)
            ->set('email', $registrado->email)
            ->call('sendResetLink')
            ->assertHasNoErrors()
            ->assertSet('linkSent', true);

        Livewire::test(ForgotPassword::class)
            ->set('email', 'nadie@example.com')
            ->call('sendResetLink')
            ->assertHasNoErrors()
            ->assertSet('linkSent', true);
    }

    public function test_el_correo_sigue_teniendo_que_ser_valido(): void
    {
        Livewire::test(ForgotPassword::class)
            ->set('email', 'esto-no-es-un-correo')
            ->call('sendResetLink')
            ->assertHasErrors('email')
            ->assertSet('linkSent', false);
    }

    public function test_el_token_de_recuperacion_no_llega_a_los_logs(): void
    {
        // El middleware solo registra en local, o por encima de su umbral de
        // lentitud. Sin esto no habría nada que inspeccionar.
        $this->app->detectEnvironment(fn (): string => 'local');

        Log::spy();

        $this->get('/reset-password/token-de-un-solo-uso');

        Log::shouldHaveReceived('info')->withArgs(
            fn (string $message, array $context): bool => $message === 'request.duration'
                && ! str_contains((string) json_encode($context), 'token-de-un-solo-uso')
        );
    }

    /**
     * El límite frena a quien recorre una lista de correos, y lo hace sin
     * anunciarse: un «demasiados intentos» que solo aparece con los correos
     * registrados vuelve a distinguirlos, que es lo que el resto de esta
     * pantalla evita.
     */
    public function test_el_envio_se_limita_sin_decirlo(): void
    {
        Notification::fake();

        // Correos distintos a propósito: `auth.passwords.throttle` ya espacia
        // los envíos de un mismo usuario, así que repetir uno solo no prueba
        // nada. Lo que este límite frena es recorrer una lista desde una IP.
        $usuarios = User::factory()->count(6)->create();

        foreach ($usuarios as $usuario) {
            Livewire::test(ForgotPassword::class)
                ->set('email', $usuario->email)
                ->call('sendResetLink')
                ->assertHasNoErrors()
                ->assertSet('linkSent', true);
        }

        Notification::assertCount(5);
    }
}
