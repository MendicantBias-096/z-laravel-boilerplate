<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * R25 · la tabla lleva el prefijo de su módulo.
 *
 * Se renombra en vez de corregir la migración original: quien ya instanció
 * este boilerplate tiene datos en `profiles`, y reescribir el `create` los
 * dejaría en una tabla que nadie vuelve a nombrar.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('profiles', 'access_profiles');
    }

    public function down(): void
    {
        Schema::rename('access_profiles', 'profiles');
    }
};
