<?php

declare(strict_types=1);

namespace App\Modules\Platform\Tests\Feature\Livewire;

use App\Modules\Platform\Traits\Livewire\HasRepeatableFields;
use Livewire\Component;
use Livewire\Livewire;
use RuntimeException;
use Tests\TestCase;

/**
 * Componente de prueba: dos campos repetibles declarados y una propiedad que
 * no lo está, que es lo que hace comprobable la guarda.
 */
class ComponenteConRepetibles extends Component
{
    /** @var list<string> */
    public array $telefonos = [''];

    /** @var list<int> */
    public array $cantidades = [0];

    /** @var list<string|null> */
    public array $tipos = [null];

    public string $secreto = 'no repetible';

    use HasRepeatableFields;

    /**
     * @return array<string, mixed>
     */
    protected function repeatableFields(): array
    {
        return ['telefonos' => '', 'cantidades' => 0, 'tipos' => null];
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                <x-ui.repeater field="telefonos" label="Teléfono" sortable />
                <x-ui.repeater field="tipos" label="Tipo" type="select" :options="['casa', 'trabajo']" />
            </div>
        BLADE;
    }
}

class RepeaterTest extends TestCase
{
    public function test_agregar_anade_una_fila_con_el_valor_declarado(): void
    {
        Livewire::test(ComponenteConRepetibles::class)
            ->call('addRepeatable', 'telefonos')
            ->assertSet('telefonos', ['', ''])
            ->call('addRepeatable', 'cantidades')
            ->assertSet('cantidades', [0, 0]);
    }

    /**
     * El hueco que deja `unset` es el fallo clásico: `wire:model="campo.1"`
     * apunta a un índice que ya no existe y la fila deja de responder.
     */
    public function test_eliminar_reindexa_el_array(): void
    {
        Livewire::test(ComponenteConRepetibles::class)
            ->set('telefonos', ['uno', 'dos', 'tres'])
            ->call('removeRepeatable', 'telefonos', 1)
            ->assertSet('telefonos', ['uno', 'tres']);
    }

    public function test_reordenar_intercambia_las_filas(): void
    {
        Livewire::test(ComponenteConRepetibles::class)
            ->set('telefonos', ['uno', 'dos', 'tres'])
            ->call('moveRepeatable', 'telefonos', 2, -1)
            ->assertSet('telefonos', ['uno', 'tres', 'dos'])
            ->call('moveRepeatable', 'telefonos', 0, 1)
            ->assertSet('telefonos', ['tres', 'uno', 'dos']);
    }

    /**
     * El índice y la dirección llegan del navegador, así que un destino fuera
     * del array no es una hipótesis.
     */
    public function test_reordenar_fuera_de_rango_no_hace_nada(): void
    {
        Livewire::test(ComponenteConRepetibles::class)
            ->set('telefonos', ['uno', 'dos'])
            ->call('moveRepeatable', 'telefonos', 0, -1)
            ->assertSet('telefonos', ['uno', 'dos'])
            ->call('moveRepeatable', 'telefonos', 1, 1)
            ->assertSet('telefonos', ['uno', 'dos'])
            ->call('moveRepeatable', 'telefonos', 0, 5)
            ->assertSet('telefonos', ['uno', 'dos']);
    }

    /**
     * La guarda que justifica que `repeatableFields()` sea obligatorio: los
     * tres métodos son públicos y el nombre del campo lo elige el navegador.
     */
    public function test_un_campo_no_declarado_no_se_puede_tocar(): void
    {
        $this->expectException(RuntimeException::class);

        Livewire::test(ComponenteConRepetibles::class)
            ->call('removeRepeatable', 'secreto', 0);
    }

    public function test_la_vista_pinta_una_fila_por_valor(): void
    {
        Livewire::test(ComponenteConRepetibles::class)
            ->set('telefonos', ['uno', 'dos'])
            ->assertSeeHtml('wire:model="telefonos.0"')
            ->assertSeeHtml('wire:model="telefonos.1"')
            ->assertDontSeeHtml('wire:model="telefonos.2"');
    }

    /**
     * La otra rama de la vista. Sin este caso, `type="select"` solo se
     * descubre roto al usarlo.
     */
    public function test_la_vista_pinta_un_desplegable_cuando_se_le_pide(): void
    {
        Livewire::test(ComponenteConRepetibles::class)
            ->set('tipos', [null, 'casa'])
            ->assertSeeHtml('wire:model="tipos.0"')
            ->assertSeeHtml('wire:model="tipos.1"')
            ->assertSee('trabajo');
    }
}
