<?php

namespace App\Modules\Access\Database\Seeders;

use App\Modules\Access\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Las cuentas de ejemplo, únicas que `quickLogin` acepta.
     *
     * La lista vive aquí y no en el componente para que sembrar una cuenta
     * nueva no habilite el acceso sin contraseña por olvido: quien la añada
     * aquí está eligiendo las dos cosas a la vez, y se ve en el mismo diff.
     *
     * @var list<string>
     */
    public const DEMO_EMAILS = ['admin@example.com', 'user@example.com'];

    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'username' => 'admin',
                'password' => Hash::make('zygma-online-boilerplate-2026-1.0.0'),
                'email_verified_at' => now(),
                'is_protected' => true,
            ]
        );
        $admin->syncRoles('admin');

        $user = User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'username' => 'usuario',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $user->syncRoles('user');
    }
}
