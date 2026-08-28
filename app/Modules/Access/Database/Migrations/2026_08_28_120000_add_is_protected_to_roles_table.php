<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Distingue los roles que declara el código de los que crea el cliente.
     *
     * El seeder hace `syncPermissions` y reimpone `display_name` sobre los
     * roles de `Access/Config/permissions.php`, así que editarlos por la
     * interfaz dura hasta el siguiente `db:seed`. La columna permite decirlo
     * en vez de dejar que el cambio se deshaga solo.
     */
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table): void {
            $table->boolean('is_protected')->default(false)->after('display_name');
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table): void {
            $table->dropColumn('is_protected');
        });
    }
};
