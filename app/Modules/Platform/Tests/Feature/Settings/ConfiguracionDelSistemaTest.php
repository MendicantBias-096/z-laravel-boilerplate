<?php

declare(strict_types=1);

namespace App\Modules\Platform\Tests\Feature\Settings;

use App\Modules\Access\Models\User;
use App\Modules\Platform\Livewire\Settings\SystemForm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * `mount()` corre una vez; `save()` corre en cada petición.
 *
 * Autorizar solo al montar deja el componente abierto durante toda la vida de
 * la pestaña: quien pierde el permiso conserva un snapshot firmado y sigue
 * guardando. Es la mitad de R58 que no se ve leyendo el componente, porque el
 * `authorize()` de `mount()` parece cubrirlo.
 */
class ConfiguracionDelSistemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_guardar_revalida_el_permiso_despues_de_montar(): void
    {
        $actor = $this->userWithSettingsPermission();

        $componente = Livewire::actingAs($actor)->test(SystemForm::class);

        $actor->revokePermissionTo('platform.settings.manage');

        $componente->set('app_name', 'Suplantado')
            ->call('save')
            ->assertForbidden();

        $this->assertDatabaseMissing('platform_settings', ['value' => 'Suplantado']);
    }

    public function test_quien_conserva_el_permiso_guarda_la_configuracion(): void
    {
        $actor = $this->userWithSettingsPermission();

        Livewire::actingAs($actor)
            ->test(SystemForm::class)
            ->set('app_name', 'Boilerplate')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('platform_settings', [
            'key' => 'app_name',
            'value' => 'Boilerplate',
        ]);
    }

    private function userWithSettingsPermission(): User
    {
        $this->seedRoles();

        $user = User::factory()->create();
        $user->givePermissionTo('platform.settings.manage');

        return $user;
    }
}
