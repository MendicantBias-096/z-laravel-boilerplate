<?php

declare(strict_types=1);

namespace Tests\Arch;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use PHPat\Selector\Selector;
use PHPat\Test\Builder\Rule;
use PHPat\Test\PHPat;

/**
 * Las reglas de docs/ARCHITECTURE_RULES.md que son aserciones de dependencia.
 *
 * Corren dentro de PHPStan, así que no hay un segundo binario ni una segunda
 * pasada de CI. Lo que no es una dependencia entre clases —semántica de una
 * llamada, forma de una firma, ciclos— no vive aquí: va a `arch:check`.
 *
 * Dos grupos:
 *   vigentes    aplican a la estructura actual, bajo `app/`
 *   de módulo   escritas contra `App\Modules\*`, que todavía no existe; se
 *               activan solas el día que se cree el primer módulo
 *
 * Las de módulo que exigen conocer los nombres de los módulos de negocio
 * —«Billing solo puede importar Contracts de Inventory»— no se pueden escribir
 * genéricas y se añaden con cada módulo. Las de aquí son las que no dependen
 * de esos nombres: la plataforma y el interior de un módulo cualquiera.
 */
final class ArchitectureRules
{
    private const MODULE = '/^App\\\\Modules\\\\[A-Za-z]+';

    /**
     * R17 · La UI es la punta: nada del resto del código la importa.
     *
     * Es lo que permite que un módulo no tenga pantallas (R3). Vigente hoy
     * sobre `App\Livewire`, y es la única de este archivo que ya tiene código
     * al que aplicarse.
     */
    public function test_r17_nada_importa_livewire(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('App'))
            ->excluding(
                Selector::inNamespace('App\Livewire'),
                Selector::inNamespace('App\Providers'),
            )
            ->shouldNotDependOn()
            ->classes(Selector::inNamespace('App\Livewire'))
            ->because('la UI es la punta: si algo de abajo la importa, el módulo ya no puede existir sin pantallas (R17)');
    }

    /**
     * R9 · `Platform` es la base del grafo y no depende de nadie.
     *
     * Cicatriz: `NotificationsService` vivía en Platform y hacía
     * `User::permission($p)->get()` — la base preguntándole a la capa de
     * encima. Se invierte con un contrato que Platform define y Access
     * implementa.
     */
    public function test_r9_platform_no_depende_de_nadie(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('App\Modules\Platform'))
            ->shouldNotDependOn()
            ->classes(Selector::inNamespace(self::MODULE.'/', true))
            ->excluding(Selector::inNamespace('App\Modules\Platform'))
            ->because('Platform es la base del grafo; lo que necesite de arriba se invierte con un contrato (R9)');
    }

    /** R9 · `Access` solo mira hacia abajo, y abajo solo está `Platform`. */
    public function test_r9_access_solo_depende_de_platform(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('App\Modules\Access'))
            ->shouldNotDependOn()
            ->classes(Selector::inNamespace(self::MODULE.'/', true))
            ->excluding(
                Selector::inNamespace('App\Modules\Access'),
                Selector::inNamespace('App\Modules\Platform'),
            )
            ->because('Access es plataforma: no puede depender de un módulo de negocio (R9)');
    }

    /**
     * R13 · Un contrato devuelve DTOs, nunca modelos.
     *
     * Un modelo Eloquent no es una clase, es un grafo navegable: publicarlo
     * publica su clausura entera. Es la regla que hace que R8 se sostenga.
     */
    public function test_r13_contracts_no_dependen_de_models(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace(self::MODULE.'\\\\Contracts/', true))
            ->shouldNotDependOn()
            ->classes(Selector::inNamespace(self::MODULE.'\\\\Models/', true))
            ->because('un contrato que devuelve un modelo es un import disfrazado, y arrastra sus relaciones al otro lado de la frontera (R13)');
    }

    /**
     * R17 · Un modelo no llama al caso de uso.
     *
     * Si lo hace, la lógica vuelve a esconderse dentro del modelo, que es de
     * donde R19 la saca.
     */
    public function test_r17_models_no_dependen_de_actions(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace(self::MODULE.'\\\\Models/', true))
            ->shouldNotDependOn()
            ->classes(Selector::inNamespace(self::MODULE.'\\\\Actions/', true))
            ->because('si el modelo llama al Action, la lógica se esconde donde nadie la busca (R17)');
    }

    /**
     * R21 · Un caso de uso es una clase final con un solo método público.
     *
     * `handle()` y no `__invoke()` porque es greppable: `grep -r "handle("`
     * lista los casos de uso de un módulo que no conoces.
     */
    public function test_r21_actions_son_final(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace(self::MODULE.'\\\\Actions/', true))
            ->shouldBeFinal()
            ->because('un caso de uso se compone, no se extiende (R21)');
    }

    /** R21 · Y ese método se llama `handle`. */
    public function test_r21_actions_tienen_un_solo_metodo_handle(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace(self::MODULE.'\\\\Actions/', true))
            ->shouldHaveOnlyOnePublicMethodNamed('handle')
            ->because('un archivo, una acción, un test; en cuanto hay dos métodos públicos vuelve a ser un Service (R21)');
    }

    /**
     * R24 · Un DTO es una `final readonly class` de PHP.
     *
     * Seis líneas, cero dependencias, y PHPStan lo entiende completo. Se
     * descartó spatie/laravel-data: resuelve problemas que este proyecto no
     * tiene y los heredarían todos los productos instanciados.
     */
    public function test_r24_los_dtos_son_readonly(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace(self::MODULE.'\\\\Data/', true))
            ->shouldBeReadonly()
            ->because('un DTO es una caja de datos: si se puede mutar, deja de ser una foto del instante (R24)');
    }

    /** R24 · Y final, por la misma razón que el Action. */
    public function test_r24_los_dtos_son_final(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace(self::MODULE.'\\\\Data/', true))
            ->shouldBeFinal()
            ->because('un DTO no se extiende: se construye otro (R24)');
    }

    /**
     * R14 · Un evento entre módulos se despacha después del commit.
     *
     * Sin esto el listener manda el correo sobre una factura que el rollback
     * dejó sin existir. La otra mitad de R14 —que el listener sea
     * `ShouldQueue`— no se puede afirmar aquí: el vínculo evento-listener se
     * resuelve en runtime, así que vive en un test.
     */
    public function test_r14_los_eventos_se_despachan_tras_el_commit(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace(self::MODULE.'\\\\Events/', true))
            ->shouldImplement()
            ->classes(Selector::classname(ShouldDispatchAfterCommit::class))
            ->because('un evento despachado dentro de la transacción avisa de algo que puede no haber ocurrido (R14)');
    }
}
