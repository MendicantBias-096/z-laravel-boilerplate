<?php

namespace Tests\Feature\Personal;

use App\Traits\Livewire\HasSoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Js;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Livewire;
use TallStackUi\Traits\Interactions;
use Tests\TestCase;

/**
 * `HasSoftDeletes` con una clave que no es un entero.
 *
 * El id llega como texto desde el navegador. Con `$table->id()` PHP coacciona
 * `"12"` a `12` sin quejarse, así que las firmas tipadas `int` funcionaban por
 * accidente y ningún test de este repo las tumbaba. Con UUID o ULID no hay
 * número que sacar: revientan con `TypeError` en PHP 8.5, y en versiones
 * anteriores llegaban como `0` —`find(0)` no encontraba nada y el toast de
 * éxito salía igual, que es el fallo peligroso porque nadie vuelve a mirar—.
 *
 * Ninguno de los modelos del boilerplate usa clave no numérica, así que el
 * modelo de este test existe sólo para eso: sin él, revertir las firmas a `int`
 * dejaría la suite entera en verde (DAYA-237, LDT-6).
 */
class ClaveNoNumericaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('registros_de_prueba', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('nombre');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function test_a_row_with_a_uuid_key_can_be_soft_deleted(): void
    {
        $this->actingAs($this->createAdmin());
        $registro = RegistroDePrueba::create(['nombre' => 'Uno']);

        // El id viaja como texto, igual que desde el navegador.
        Livewire::test(TablaDePrueba::class)
            ->call('softDelete', (string) $registro->id)
            ->assertOk();

        $this->assertSoftDeleted($registro);
    }

    public function test_a_row_with_a_uuid_key_can_be_restored(): void
    {
        $this->actingAs($this->createAdmin());
        $registro = RegistroDePrueba::create(['nombre' => 'Dos']);
        $registro->delete();

        Livewire::test(TablaDePrueba::class)
            ->call('restore', (string) $registro->id)
            ->assertOk();

        $this->assertNotSoftDeleted($registro);
    }

    public function test_the_confirmation_dialogs_accept_a_text_id(): void
    {
        // El diálogo va antes que el borrado: si `confirmDelete()` revienta con
        // el UUID, el usuario nunca llega a la acción que sí estaba arreglada.
        $this->actingAs($this->createAdmin());
        $registro = RegistroDePrueba::create(['nombre' => 'Tres']);

        Livewire::test(TablaDePrueba::class)
            ->call('confirmDelete', (string) $registro->id)
            ->assertOk()
            ->call('confirmRestore', (string) $registro->id)
            ->assertOk();
    }

    /**
     * Una acción que no hace nada tiene que decir por qué.
     *
     * Entre que se pinta la fila y se confirma el diálogo pasa tiempo real, y
     * otro usuario pudo borrarla. Con el `find($id)?->delete()` de antes, el
     * toast de éxito salía igual con la fila intacta delante.
     */
    public function test_deleting_a_row_that_is_already_gone_reports_an_error(): void
    {
        $this->actingAs($this->createAdmin());
        $registro = RegistroDePrueba::create(['nombre' => 'Cuatro']);
        $id = (string) $registro->id;
        $registro->forceDelete();

        Livewire::test(TablaDePrueba::class)
            ->call('softDelete', $id)
            ->assertDispatched(
                'ts-ui:toast',
                fn (string $event, array $params) => $params['description'] === __('app.not_found', ['model' => 'Registro'])
            );
    }

    /**
     * La otra mitad del bug, la del navegador.
     *
     * Livewire evalúa el contenido de `wire:click` como expresión, así que un
     * id sin comillas muere en un `SyntaxError` antes de llegar al servidor.
     * `Js::from()` cita las cadenas y deja los enteros como números.
     */
    public function test_a_text_id_reaches_the_browser_quoted(): void
    {
        $uuid = (string) Str::uuid7();
        $ulid = (string) Str::ulid();

        $this->assertSame("'{$uuid}'", (string) Js::from($uuid));
        $this->assertSame("'{$ulid}'", (string) Js::from($ulid));
        $this->assertSame('12', (string) Js::from(12));
    }
}

/** Modelo de prueba: existe sólo para tener una clave que no es un entero. */
class RegistroDePrueba extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'registros_de_prueba';

    protected $fillable = ['nombre'];
}

/** El componente mínimo que usa el trait bajo prueba. */
class TablaDePrueba extends Component
{
    use HasSoftDeletes, Interactions;

    protected string $modelClass = RegistroDePrueba::class;

    protected string $deletePermission = 'eliminar usuarios';

    protected string $restorePermission = 'restaurar usuarios';

    protected string $modelLabel = 'Registro';

    public function render(): string
    {
        return '<div></div>';
    }
}
