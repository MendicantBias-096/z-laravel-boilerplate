<?php

declare(strict_types=1);

namespace App\Modules\Platform\Tests\Feature\Livewire;

use App\Modules\Platform\Traits\Livewire\HasRepeatableBlocks;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\View;
use Livewire\Component;
use Livewire\Livewire;
use Livewire\WithFileUploads;
use RuntimeException;
use Tests\TestCase;

class ComponenteConBloques extends Component
{
    use HasRepeatableBlocks;
    use WithFileUploads;

    /** @var array<string, array<string, mixed>> */
    public array $experimentos = [];

    public string $secreto = 'no repetible';

    public function mount(): void
    {
        $this->ensureOneBlock('experimentos');
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    protected function repeatableBlocks(): array
    {
        return ['experimentos' => ['nombre' => '', 'comprobante' => null]];
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                <x-ui.block-repeater field="experimentos" row-view="repeater-test::fila" label="Experimento" sortable />
            </div>
        BLADE;
    }
}

/**
 * El caso que justifica que este repetidor no sea el otro.
 *
 * Con índices, borrar un bloque corre los de después una posición y el archivo
 * que se subió en uno aparece en el vecino. Con claves propias no hay nada que
 * correr, y eso es lo que estos casos comprueban de verdad.
 */
class BlockRepeaterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        View::addNamespace('repeater-test', __DIR__.'/fixtures');
    }

    public function test_cada_bloque_nace_con_su_propia_clave(): void
    {
        $componente = Livewire::test(ComponenteConBloques::class)
            ->call('addBlock', 'experimentos');

        $claves = array_keys($componente->get('experimentos'));

        $this->assertCount(2, $claves);
        $this->assertSame($claves, array_unique($claves));
        $this->assertNotSame(['0', '1'], $claves, 'Las claves son posiciones, y ese es exactamente el fallo.');
    }

    public function test_eliminar_un_bloque_no_mueve_los_archivos_de_los_demas(): void
    {
        $componente = Livewire::test(ComponenteConBloques::class)
            ->call('addBlock', 'experimentos')
            ->call('addBlock', 'experimentos');

        [$primera, $segunda, $tercera] = array_keys($componente->get('experimentos'));

        $componente
            ->set("experimentos.{$primera}.comprobante", UploadedFile::fake()->create('primero.pdf'))
            ->set("experimentos.{$segunda}.comprobante", UploadedFile::fake()->create('segundo.pdf'))
            ->set("experimentos.{$tercera}.comprobante", UploadedFile::fake()->create('tercero.pdf'))
            ->call('removeBlock', 'experimentos', $segunda);

        $bloques = $componente->get('experimentos');

        $this->assertSame([$primera, $tercera], array_keys($bloques));
        $this->assertSame('primero.pdf', $bloques[$primera]['comprobante']->getClientOriginalName());
        $this->assertSame('tercero.pdf', $bloques[$tercera]['comprobante']->getClientOriginalName());
    }

    public function test_reordenar_cambia_el_orden_y_no_las_claves(): void
    {
        $componente = Livewire::test(ComponenteConBloques::class)
            ->call('addBlock', 'experimentos');

        [$primera, $segunda] = array_keys($componente->get('experimentos'));

        $componente
            ->set("experimentos.{$primera}.nombre", 'uno')
            ->set("experimentos.{$segunda}.nombre", 'dos')
            ->call('moveBlock', 'experimentos', $segunda, -1);

        $bloques = $componente->get('experimentos');

        $this->assertSame([$segunda, $primera], array_keys($bloques));
        $this->assertSame('dos', $bloques[$segunda]['nombre']);
        $this->assertSame('uno', $bloques[$primera]['nombre']);
    }

    public function test_reordenar_fuera_de_rango_no_hace_nada(): void
    {
        $componente = Livewire::test(ComponenteConBloques::class);
        [$unica] = array_keys($componente->get('experimentos'));

        $componente->call('moveBlock', 'experimentos', $unica, -1);
        $this->assertSame([$unica], array_keys($componente->get('experimentos')));

        $componente->call('moveBlock', 'experimentos', 'clave-inventada', 1);
        $this->assertSame([$unica], array_keys($componente->get('experimentos')));
    }

    public function test_un_bloque_no_declarado_no_se_puede_tocar(): void
    {
        $this->expectException(RuntimeException::class);

        Livewire::test(ComponenteConBloques::class)->call('addBlock', 'secreto');
    }

    public function test_la_vista_repite_la_fila_del_consumidor_con_su_clave(): void
    {
        $componente = Livewire::test(ComponenteConBloques::class)->call('addBlock', 'experimentos');

        [$primera, $segunda] = array_keys($componente->get('experimentos'));

        $componente
            ->assertSeeHtml("experimentos.{$primera}.nombre")
            ->assertSeeHtml("experimentos.{$segunda}.comprobante")
            ->assertSee('Experimento');
    }
}
