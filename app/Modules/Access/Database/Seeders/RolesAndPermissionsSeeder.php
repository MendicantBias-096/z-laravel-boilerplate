<?php

namespace App\Modules\Access\Database\Seeders;

use App\Modules\Access\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear todos los permisos definidos en config/roles.php
        foreach (config('roles.permissions') as $module => $permissions) {
            foreach ($permissions as $permission) {
                Permission::firstOrCreate(['name' => $permission]);
            }
        }

        // Crear roles y asignarles los permisos de sus módulos
        foreach (config('roles.roles') as $roleName => $displayName) {
            // `is_protected` se marca aquí y en ningún otro sitio: estar en
            // este bucle es exactamente lo que hace protegido a un rol, porque
            // es lo que provoca que el seeder lo devuelva a su sitio.
            $role = Role::firstOrCreate(
                ['name' => $roleName],
                ['display_name' => $displayName, 'is_protected' => true],
            );

            if (! $role->wasRecentlyCreated) {
                $role->update(['display_name' => $displayName, 'is_protected' => true]);
            }

            $modules = config("roles.roles_modules.{$roleName}", []);

            $permissions = collect($modules)
                ->flatMap(fn ($module) => config("roles.permissions.{$module}", []))
                ->all();

            if (count($permissions) > 0) {
                $role->syncPermissions($permissions);
            }
        }
    }
}
