<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('password'),
            ]
        );

        $admin->assignRole(Roles::ADMIN->value);

        // Asignar todos los permisos del sistema al admin
        $allPermissions = collect(config('roles.permissions'))
            ->flatten()
            ->all();

        $admin->syncPermissions($allPermissions);
    }
}
