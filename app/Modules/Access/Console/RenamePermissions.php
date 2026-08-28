<?php

declare(strict_types=1);

namespace App\Modules\Access\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

/**
 * Renombra los permisos en español al formato de R40.
 *
 * Va en un comando y no en una migración porque transforma datos (R38): un
 * `UPDATE` en una migración solo se ejecuta una vez, en producción, y en
 * `migrate:fresh` no hace nada, así que nunca se prueba donde se escribe.
 *
 * Renombra la fila en vez de borrarla y recrearla. `model_has_permissions` y
 * `role_has_permissions` apuntan por `permission_id`, así que un delete más
 * insert daría ids nuevos y dejaría a todo el mundo sin sus permisos, en
 * silencio. Un `update` del nombre conserva la fila y con ella las
 * asignaciones.
 *
 * Idempotente: una instalación nueva ya siembra los nombres finales y el
 * comando no encuentra nada que renombrar.
 */
class RenamePermissions extends Command
{
    protected $signature = 'access:rename-permissions {--dry-run : Solo informar}';

    protected $description = 'Renombra los permisos en español al formato {modulo}.{recurso}.{accion} (R40)';

    /** Nombre antiguo => nombre de R40. */
    private const MAP = [
        'ver usuarios' => 'access.users.view',
        'crear usuarios' => 'access.users.create',
        'editar usuarios' => 'access.users.update',
        'eliminar usuarios' => 'access.users.delete',
        'restaurar usuarios' => 'access.users.restore',
        'ver roles' => 'access.roles.view',
        'crear roles' => 'access.roles.create',
        'editar roles' => 'access.roles.update',
        'eliminar roles' => 'access.roles.delete',
        'administrar sistema' => 'platform.settings.manage',
        'notificacion usuario creado' => 'access.notifications.user-created',
        'notificacion usuario editado' => 'access.notifications.user-updated',
        'notificacion usuario eliminado' => 'access.notifications.user-deleted',
        'notificacion rol creado' => 'access.notifications.role-created',
        'notificacion rol actualizado' => 'access.notifications.role-updated',
        'notificacion rol eliminado' => 'access.notifications.role-deleted',
    ];

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $renombrados = 0;

        foreach (self::MAP as $viejo => $nuevo) {
            $fila = DB::table('permissions')->where('name', $viejo)->first();

            if ($fila === null) {
                continue;
            }

            // Si el destino ya existe, renombrar chocaría con el índice único.
            // Pasa cuando el comando corre a medias y se repite.
            if (DB::table('permissions')->where('name', $nuevo)->exists()) {
                $this->warn("«{$nuevo}» ya existe; «{$viejo}» se queda como está.");

                continue;
            }

            $this->line("  {$viejo}  →  {$nuevo}");
            $renombrados++;

            if (! $dry) {
                DB::table('permissions')->where('id', $fila->id)->update(['name' => $nuevo]);
            }
        }

        if (! $dry) {
            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }

        $this->newLine();
        $this->info($dry
            ? "{$renombrados} permisos se renombrarían."
            : "{$renombrados} permisos renombrados.");

        return self::SUCCESS;
    }
}
