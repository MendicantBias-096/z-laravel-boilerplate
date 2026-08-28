<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * R25 · la tabla lleva el prefijo de su módulo.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('settings', 'platform_settings');
    }

    public function down(): void
    {
        Schema::rename('platform_settings', 'settings');
    }
};
