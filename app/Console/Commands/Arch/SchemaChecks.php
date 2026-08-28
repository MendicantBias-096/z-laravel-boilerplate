<?php

declare(strict_types=1);

namespace App\Console\Commands\Arch;

use App\Console\Commands\ArchCheck;
use Illuminate\Support\Facades\DB;

/**
 * Las reglas que solo sabe el esquema ya migrado: R27, R30 y R32.
 *
 * Viven fuera del comando por dos razones. Comparten la consulta —preguntar
 * tres veces por separado triplica el coste de abrir la conexión— y son las
 * únicas que no se pueden comprobar sin base de datos, así que separarlas hace
 * evidente qué se pierde cuando no la hay.
 */
class SchemaChecks
{
    private bool $fallo = false;

    public function __construct(private readonly ArchCheck $cmd) {}

    /**
     * R27, R30 y R32 · lo que solo sabe el esquema ya migrado.
     *
     * Van juntas porque comparten la consulta: preguntar tres veces por
     * separado triplica el coste de arrancar la conexión.
     */
    public function run(?string $only): bool
    {
        try {
            /** @var list<array{tabla: string, referida: string, al_borrar: string}> $fks */
            $fks = $this->rows("
                select tc.table_name as tabla,
                       ccu.table_name as referida,
                       rc.delete_rule as al_borrar
                from information_schema.table_constraints tc
                join information_schema.constraint_column_usage ccu
                    on ccu.constraint_name = tc.constraint_name
                join information_schema.referential_constraints rc
                    on rc.constraint_name = tc.constraint_name
                where tc.constraint_type = 'FOREIGN KEY'
                  and tc.table_schema = current_schema()
            ");
            /** @var list<array{tabla: string, tipo: string}> $claves */
            $claves = $this->rows("
                select c.table_name as tabla, c.data_type as tipo
                from information_schema.columns c
                join information_schema.table_constraints tc
                    on tc.table_name = c.table_name and tc.constraint_type = 'PRIMARY KEY'
                join information_schema.key_column_usage k
                    on k.constraint_name = tc.constraint_name and k.column_name = c.column_name
                where c.table_schema = current_schema() and c.column_name = 'id'
            ");
        } catch (\Throwable $fallo) {
            $this->cmd->line('<fg=yellow>! R27 R30 R32</>  sin base de datos: '.$fallo->getMessage());

            return false;
        }

        $exentas = config('arch.exempt_tables', []);
        $prefijos = config('arch.module_prefixes', []);

        if ($only === null || $only === 'R27') {
            $this->checkCascades($fks);
        }

        if ($only === null || $only === 'R32') {
            $this->checkCrossModuleKeys($fks, $exentas, $prefijos);
        }

        if ($only === null || $only === 'R30') {
            $this->checkKeyTypes($claves, array_values(array_map(strval(...), array_merge($exentas, config('arch.integer_key_tables', [])))));
        }

        return $this->fallo;
    }

    /**
     * `DB::select` devuelve stdClass y level 8 no puede tipar sus propiedades;
     * como array con forma declarada sí.
     *
     * @return list<array<string, string>>
     */
    private function rows(string $sql): array
    {
        return array_values(array_map(
            /** @return array<string, string> */
            fn (object $fila): array => array_map(strval(...), (array) $fila),
            DB::select($sql)
        ));
    }

    /** @param  list<array{tabla: string, referida: string, al_borrar: string}>  $fks */
    private function checkCascades(array $fks): void
    {
        $malas = [];

        foreach ($fks as $fk) {
            if ($fk['al_borrar'] === 'CASCADE') {
                $malas[] = "{$fk['tabla']} → {$fk['referida']}";
            }
        }

        if ($malas === []) {
            $this->cmd->line('<fg=green>✓ R27</>  ninguna FK borra en cascada');

            return;
        }

        // Aviso y no error: `access_profiles` cascadea a propósito, y la regla
        // habla de las FK entre módulos de negocio. Con el primero se decide.
        $this->cmd->line(sprintf('<fg=yellow>! R27</>  %d FK en cascada, revisar que el ciclo de vida sea el querido:', count($malas)));

        foreach (array_slice($malas, 0, 6) as $mala) {
            $this->cmd->line('      '.$mala);
        }
    }

    /**
     * @param  list<array{tabla: string, referida: string, al_borrar: string}>  $fks
     * @param  list<string>  $exentas
     * @param  list<string>  $prefijos
     */
    private function checkCrossModuleKeys(array $fks, array $exentas, array $prefijos): void
    {
        $cruzadas = [];

        foreach ($fks as $fk) {
            $origen = $this->modulo($fk['tabla'], $prefijos);
            $destino = $this->modulo($fk['referida'], $prefijos);

            if ($origen === null || $destino === null || $origen === $destino) {
                continue;
            }

            // Apuntar a una tabla de plataforma o de paquete está permitido:
            // lo que R32 prohíbe es atar dos módulos de negocio.
            if (in_array($fk['referida'], $exentas, true)) {
                continue;
            }

            $cruzadas[] = "{$fk['tabla']} → {$fk['referida']}";
        }

        if ($cruzadas === []) {
            $this->cmd->line('<fg=green>✓ R32</>  ninguna FK cruza la frontera de un módulo');

            return;
        }

        $this->fallo = true;
        $this->cmd->line(sprintf('<fg=red>✗ R32</>  %d FK cruzan módulos:', count($cruzadas)));

        foreach ($cruzadas as $cruzada) {
            $this->cmd->line('      '.$cruzada);
        }
    }

    /**
     * @param  list<array{tabla: string, tipo: string}>  $claves
     * @param  list<string>  $exentas
     */
    private function checkKeyTypes(array $claves, array $exentas): void
    {
        $enteras = [];

        foreach ($claves as $clave) {
            if (in_array($clave['tabla'], $exentas, true)) {
                continue;
            }

            if (! in_array($clave['tipo'], ['uuid', 'character', 'character varying', 'text'], true)) {
                $enteras[] = "{$clave['tabla']} ({$clave['tipo']})";
            }
        }

        if ($enteras === []) {
            $this->cmd->line('<fg=green>✓ R30</>  toda tabla de dominio tiene clave no enumerable');

            return;
        }

        $this->fallo = true;
        $this->cmd->line(sprintf('<fg=red>✗ R30</>  %d tablas con clave entera fuera de la lista exenta:', count($enteras)));

        foreach ($enteras as $entera) {
            $this->cmd->line('      '.$entera);
        }
    }

    /** @param  list<string>  $prefijos */
    private function modulo(string $tabla, array $prefijos): ?string
    {
        foreach ($prefijos as $prefijo) {
            if (str_starts_with($tabla, $prefijo.'_')) {
                return $prefijo;
            }
        }

        return null;
    }
}
