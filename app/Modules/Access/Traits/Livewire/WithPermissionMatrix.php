<?php

declare(strict_types=1);

namespace App\Modules\Access\Traits\Livewire;

use Livewire\Attributes\Computed;

/**
 * La matriz de permisos: la misma en Usuarios y en Roles.
 *
 * Las dos pantallas reparten los mismos permisos sobre la misma rejilla de
 * grupo → módulo → verbo, y sobre las mismas maestras de fila y de columna. Lo
 * único que cambia es a quién se le asignan al guardar, así que el trait se
 * queda con la estructura y las acciones, y cada componente pone su
 * persistencia.
 *
 * No es partir un componente gordo en archivos —eso esconde el problema—: es
 * comportamiento que de verdad comparten dos pantallas, y duplicarlo garantiza
 * que dentro de un mes se vean diferente.
 *
 * Quien lo use debe declarar `public array $permissionList`.
 */
trait WithPermissionMatrix
{
    /**
     * El eje de columnas de la matriz, en orden canónico.
     *
     * Son los cuatro verbos que casi todos los módulos tienen. Lo que sobra
     * —`restore`, `manage`— no gana columna propia: una columna que casi
     * ninguna fila puede llenar es ruido, no información. Esos aparecen como
     * etiquetas en la última celda.
     *
     * @var list<string>
     */
    public const CRUD_VERBS = ['view', 'create', 'update', 'delete'];

    /**
     * Texto de la región viva de la matriz.
     *
     * Alternar un módulo o una columna voltea varias casillas de golpe y el
     * lector de pantalla solo anuncia aquella donde está el foco. Sin esto,
     * quien no ve la pantalla oye «marcada» y no sabe qué acaba de conceder.
     */
    public string $permissionAnnouncement = '';

    /**
     * Acota la matriz a unos módulos concretos.
     *
     * Un catálogo y no una caja de texto: se busca «enséñame este módulo
     * entero», y escribir a ciegas deja fuera los grupos.
     *
     * Admite `null` a propósito: al limpiarse, el select manda `null` en vez
     * de un array vacío. Con el tipo estricto `array`, Livewire deja la
     * propiedad SIN INICIALIZAR y la siguiente lectura muere con
     * «Property not found» —un mensaje que apunta al sitio equivocado, porque
     * la propiedad existe: lo que no tiene es valor—.
     *
     * @var list<string>|null
     */
    public ?array $moduleFilter = [];

    /**
     * Los permisos por grupo → módulo, tal como los declara la configuración.
     *
     * @return array<string, array<string, list<string>>>
     */
    #[Computed]
    public function permissionsByGroup(): array
    {
        $groups = config('roles.module_groups', []);
        $allModules = config('roles.permissions', []);
        $result = [];

        foreach ($groups as $group => $modules) {
            foreach ($modules as $module) {
                if (isset($allModules[$module])) {
                    $result[$group][$module] = $allModules[$module];
                }
            }
        }

        $grouped = $groups === [] ? [] : array_merge(...array_values($groups));

        foreach ($allModules as $module => $permissions) {
            if (! in_array($module, $grouped, true)) {
                $result['other'][$module] = $permissions;
            }
        }

        return $this->applyModuleFilter($result);
    }

    /**
     * El catálogo del filtro, agrupado por dominio y en el orden en que se
     * dibujan las tablas: dos ordenaciones distintas para la misma lista
     * obligan a buscar dos veces.
     *
     * @return list<array{label: string, value: string}>
     */
    #[Computed]
    public function moduleOptions(): array
    {
        $options = [];

        foreach (config('roles.module_groups', []) as $group => $modules) {
            foreach ($modules as $module) {
                if (isset(config('roles.permissions', [])[$module])) {
                    $options[] = [
                        // El grupo va en la etiqueta porque dos grupos pueden
                        // tener un módulo con el mismo nombre, y sueltos serían
                        // indistinguibles.
                        'label' => __("access::roles.groups.{$group}").' › '.__("access::roles.modules.{$module}"),
                        'value' => $module,
                    ];
                }
            }
        }

        return $options;
    }

    /**
     * La matriz, repartida en lo que es rejilla y lo que no.
     *
     * `tables` son los grupos cuyos módulos comparten el eje CRUD: una tabla
     * por grupo, con solo las columnas que ese grupo usa de verdad. `blocks`
     * son los que no comparten eje con nadie —un grupo con un único permiso,
     * unas notificaciones que son eventos y no acciones— y se dibujan como
     * listas de etiquetas.
     *
     * Forzar todos los módulos contra un eje único dejaría media rejilla
     * vacía, y una celda vacía se lee como «no concedido» en vez de como «no
     * existe».
     *
     * @return array{tables: array<string, array{columns: list<string>, rows: array<string, array{cells: array<string, string|null>, extras: list<string>}>}>, blocks: array<string, array<string, list<string>>>}
     */
    #[Computed]
    public function permissionMatrix(): array
    {
        $tables = [];
        $blocks = [];

        foreach ($this->permissionsByGroup() as $group => $modules) {
            $columns = array_values(array_filter(
                self::CRUD_VERBS,
                fn (string $verb): bool => $this->groupUsesVerb($modules, $verb)
            ));

            if ($columns === []) {
                $blocks[$group] = $modules;

                continue;
            }

            $rows = [];

            foreach ($modules as $module => $permissions) {
                $cells = [];

                foreach ($columns as $verb) {
                    // `null` significa «este permiso no existe», y la vista lo
                    // dibuja como una raya. Una casilla apagada diría «existe
                    // y no lo has marcado», que es otra afirmación.
                    $cells[$verb] = $this->permissionFor($permissions, $verb);
                }

                $rows[$module] = [
                    'cells' => $cells,
                    'extras' => array_values(array_filter(
                        $permissions,
                        fn (string $p): bool => ! in_array($this->verbOf($p), $columns, true)
                    )),
                ];
            }

            $tables[$group] = ['columns' => $columns, 'rows' => $rows];
        }

        return ['tables' => $tables, 'blocks' => $blocks];
    }

    /** @return 'all'|'some'|'none' */
    public function columnState(string $group, string $verb): string
    {
        return $this->stateOf($this->columnPermissions($group, $verb));
    }

    /**
     * Distingue los tres estados: con solo «todos» y «no todos», 3 de 5
     * marcados se ve igual que 0 de 5 y la casilla miente.
     *
     * @return 'all'|'some'|'none'
     */
    public function moduleState(string $module): string
    {
        return $this->stateOf(config("roles.permissions.{$module}", []));
    }

    /** Normaliza el `null` que manda el select al vaciarse. */
    public function updatedModuleFilter(): void
    {
        $this->moduleFilter ??= [];
    }

    /** Enciende o apaga un verbo en todo su grupo de una vez. */
    public function toggleColumn(string $group, string $verb): void
    {
        $permissions = $this->columnPermissions($group, $verb);

        if ($permissions === []) {
            return;
        }

        $granting = $this->columnState($group, $verb) !== 'all';

        $this->permissionList = $granting
            ? array_values(array_unique([...$this->permissionList, ...$permissions]))
            : array_values(array_diff($this->permissionList, $permissions));

        $this->announce(
            $granting,
            count($permissions),
            __("access::roles.verbs.{$verb}").' · '.__("access::roles.groups.{$group}"),
        );
    }

    /** Enciende o apaga todos los permisos de un módulo. */
    public function toggleModule(string $module): void
    {
        $permissions = config("roles.permissions.{$module}", []);
        $granting = array_diff($permissions, $this->permissionList) !== [];

        $this->permissionList = $granting
            ? array_values(array_unique([...$this->permissionList, ...$permissions]))
            : array_values(array_diff($this->permissionList, $permissions));

        $this->announce($granting, count($permissions), __("access::roles.modules.{$module}"));
    }

    /**
     * @param  list<string>  $permissions
     * @return 'all'|'some'|'none'
     */
    private function stateOf(array $permissions): string
    {
        if ($permissions === []) {
            return 'none';
        }

        $selected = count(array_intersect($permissions, $this->permissionList));

        return match (true) {
            $selected === 0 => 'none',
            $selected === count($permissions) => 'all',
            default => 'some',
        };
    }

    /** Deja dicho en la región viva qué acaba de cambiar y cuánto. */
    private function announce(bool $granting, int $count, string $subject): void
    {
        $this->permissionAnnouncement = __(
            $granting ? 'platform::app.user_perm_granted' : 'platform::app.user_perm_revoked',
            ['count' => $count, 'subject' => $subject],
        );
    }

    /**
     * Deja solo los módulos elegidos, enteros. Sin selección se ven todos: el
     * filtro acota, no esconde.
     *
     * @param  array<string, array<string, list<string>>>  $groups
     * @return array<string, array<string, list<string>>>
     */
    private function applyModuleFilter(array $groups): array
    {
        $filter = $this->moduleFilter ?? [];

        if ($filter === []) {
            return $groups;
        }

        $result = [];

        foreach ($groups as $group => $modules) {
            $kept = array_intersect_key($modules, array_flip($filter));

            if ($kept !== []) {
                $result[$group] = $kept;
            }
        }

        return $result;
    }

    /** @return list<string> */
    private function columnPermissions(string $group, string $verb): array
    {
        $table = $this->permissionMatrix()['tables'][$group] ?? null;

        if ($table === null) {
            return [];
        }

        return array_values(array_filter(
            array_map(fn (array $row): ?string => $row['cells'][$verb] ?? null, $table['rows'])
        ));
    }

    /** @param  array<string, list<string>>  $modules */
    private function groupUsesVerb(array $modules, string $verb): bool
    {
        foreach ($modules as $permissions) {
            if ($this->permissionFor($permissions, $verb) !== null) {
                return true;
            }
        }

        return false;
    }

    /** @param  list<string>  $permissions */
    private function permissionFor(array $permissions, string $verb): ?string
    {
        foreach ($permissions as $permission) {
            if ($this->verbOf($permission) === $verb) {
                return $permission;
            }
        }

        return null;
    }

    /**
     * El verbo de un permiso: lo que va tras el último punto de
     * `access.users.view` (R40).
     */
    private function verbOf(string $permission): string
    {
        $partes = explode('.', $permission);

        return end($partes);
    }
}
