<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SyncRolesAndPermissions extends Command
{
    /**
     * Uso:
     *   php artisan roles:sync
     *   php artisan roles:sync --assign
     *   php artisan roles:sync --assign --role=admin
     */
    protected $signature = 'roles:sync
                            {--assign : Asignar permisos nuevos a usuarios elegibles}
                            {--role=admin : Rol base para identificar usuarios elegibles}';

    protected $description = 'Sincroniza permisos y roles desde config/roles.php. Opcionalmente asigna nuevos permisos a usuarios.';

    public function handle(): int
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ── 1. Crear permisos nuevos ──────────────────────────────────────────
        $this->info('Sincronizando permisos...');

        $created = [];

        foreach (config('roles.permissions', []) as $permissions) {
            foreach ($permissions as $permission) {
                $perm = Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);

                if ($perm->wasRecentlyCreated) {
                    $created[] = $permission;
                    $this->line("  <fg=green>+</> {$permission}");
                }
            }
        }

        if (empty($created)) {
            $this->line('  Sin permisos nuevos.');
        }

        // ── 2. Crear roles nuevos y actualizar sus permisos ───────────────────
        $this->info('Sincronizando roles...');

        foreach (config('roles.roles', []) as $roleName) {
            $role      = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $modules   = config("roles.roles_modules.{$roleName}", []);
            $rolePerms = collect($modules)
                ->flatMap(fn ($m) => config("roles.permissions.{$m}", []))
                ->all();

            $role->syncPermissions($rolePerms);

            $status = $role->wasRecentlyCreated ? '<fg=green>creado</>' : 'actualizado';
            $this->line("  {$status}: {$roleName} (" . count($rolePerms) . ' permisos)');
        }

        // ── 3. Asignar permisos nuevos a usuarios con el rol indicado ────────
        if ($this->option('assign') && ! empty($created)) {
            $roleName = $this->option('role');

            $this->info("Asignando permisos nuevos a usuarios con rol '{$roleName}'...");

            $affected = 0;

            User::whereHas('roles', fn ($q) => $q->where('name', $roleName))
                ->chunk(100, function ($users) use ($created, &$affected) {
                    foreach ($users as $user) {
                        $user->givePermissionTo($created);
                        $affected++;
                        $this->line("  <fg=green>+</> {$user->username} ({$user->email})");
                    }
                });

            $this->info($affected > 0
                ? "  {$affected} usuario(s) actualizados."
                : "  Ningún usuario con rol '{$roleName}' encontrado."
            );

        } elseif ($this->option('assign') && empty($created)) {
            $this->info('Sin permisos nuevos para asignar a usuarios.');
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->newLine();
        $this->info('✓ Sincronización completada.');

        return self::SUCCESS;
    }
}
