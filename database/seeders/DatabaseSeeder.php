<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Modules\Access\Database\Seeders\RolesAndPermissionsSeeder;
use App\Modules\Access\Database\Seeders\UsersTableSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * El punto de entrada de `db:seed` sigue siendo central: cada módulo
     * declara sus seeders y este los llama en orden. Con `use` explícito,
     * porque ya no comparten namespace con esta clase — que es justo lo que
     * dejó rota la instalación limpia cuando se movieron a su módulo.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            UsersTableSeeder::class,
        ]);
    }
}
