<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Store de caché en base de datos (`CACHE_STORE=database`) y bloqueos atómicos entre workers.
     */
    public function up(): void
    {
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            // Unix timestamp para expiración eficiente sin tipos datetime en cada fila.
            $table->bigInteger('expiration')->index();
        });

        // Evita condiciones de carrera cuando varios procesos compiten por la misma clave.
        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->bigInteger('expiration')->index();
        });
    }

    /**
     * Elimina tablas de caché en BD.
     */
    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};
