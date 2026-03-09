<?php

namespace Database\Seeders;

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
                'username'          => 'admin',
                'password'          => Hash::make('zygma-online-boilerplate-2026-1.0.0'),
                'email_verified_at' => now(),
                'is_protected'      => true,
            ]
        );
        $admin->syncRoles('admin');

        $user = User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'username'          => 'usuario',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $user->syncRoles('user');
    }
}
