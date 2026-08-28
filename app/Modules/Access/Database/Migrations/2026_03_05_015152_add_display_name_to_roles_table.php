<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table): void {
            $table->string('display_name')->nullable()->after('name');
        });

        // Poblar display_name de los roles existentes
        DB::table('roles')->get()->each(function ($role): void {
            DB::table('roles')
                ->where('id', $role->id)
                ->update([
                    'display_name' => Str::of($role->name)->replace('-', ' ')->title()->toString(),
                ]);
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table): void {
            $table->dropColumn('display_name');
        });
    }
};
