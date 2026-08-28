<?php

declare(strict_types=1);

namespace App\Modules\Access\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Reescribe las referencias polimórficas que guardaban un FQCN.
 *
 * Una columna `*_type` guarda el nombre de la clase como texto, así que
 * renombrar un namespace deja esas filas apuntando a una clase que ya no
 * existe — sin error: el modelo simplemente deja de tener roles, media o
 * notificaciones. Es lo que pasó al mover `App\Models\User` a
 * `App\Modules\Access\Models\User`, y se descubrió con un 403 al abrir
 * `/users` siendo admin.
 *
 * Va en un comando y no en una migración porque transforma datos (R38): un
 * `UPDATE` en una migración solo se ejecuta una vez, en producción, y en
 * `migrate:fresh` no hace nada, así que nunca se prueba donde se escribe.
 *
 * Con el morph map registrado en `AccessServiceProvider` esto no vuelve a
 * hacer falta: lo que se guarda pasa a ser el alias, que no cambia al mover
 * una clase de sitio.
 */
class MigrateMorphTypes extends Command
{
    protected $signature = 'access:migrate-morph-types {--dry-run : Solo informar}';

    protected $description = 'Reescribe las columnas *_type que aún guardan un namespace antiguo';

    /** Tabla => columna de tipo. */
    private const COLUMNS = [
        'model_has_roles' => 'model_type',
        'model_has_permissions' => 'model_type',
        'media' => 'model_type',
        'notifications' => 'notifiable_type',
        'audits' => 'auditable_type',
        'passkeys' => 'authenticatable_type',
    ];

    /** FQCN antiguo => alias del morph map. */
    private const MAP = [
        'App\Models\User' => 'user',
        'App\Models\Profile' => 'profile',
        'App\Models\Role' => 'role',
        'App\Models\Setting' => 'setting',
        'App\Modules\Access\Models\User' => 'user',
        'App\Modules\Access\Models\Profile' => 'profile',
        'App\Modules\Access\Models\Role' => 'role',
        'App\Modules\Platform\Models\Setting' => 'setting',
    ];

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $total = 0;

        foreach (self::COLUMNS as $table => $column) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
                continue;
            }

            foreach (self::MAP as $fqcn => $alias) {
                $query = DB::table($table)->where($column, $fqcn);
                $count = $query->count();

                if ($count === 0) {
                    continue;
                }

                $this->line(sprintf('  %s.%s  %s → %s  (%d)', $table, $column, $fqcn, $alias, $count));
                $total += $count;

                if (! $dry) {
                    $query->update([$column => $alias]);
                }
            }
        }

        if ($total === 0) {
            $this->info('No hay referencias polimórficas que reescribir.');

            return self::SUCCESS;
        }

        $this->info(sprintf('%s %d filas.', $dry ? 'Se reescribirían' : 'Reescritas', $total));

        return self::SUCCESS;
    }
}
