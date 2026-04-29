<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tokens de Laravel Sanctum: hash en BD, metadatos de uso y expiración opcional.
     */
    public function up(): void
    {
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            // Relación polimórfica hacia el modelo que recibe el token (p. ej. User).
            $table->morphs('tokenable');
            $table->text('name');
            // Solo se guarda el hash; el valor en texto plano solo existe al crear el token.
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            // Índice para limpiar o invalidar tokens caducados por lotes.
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Elimina la tabla de tokens de API.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};
