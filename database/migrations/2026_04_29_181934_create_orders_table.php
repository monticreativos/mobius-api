<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pedidos del dominio: total acumulado y ciclo de vida acotado por enum en BD.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            // Si se borra el usuario, sus pedidos se eliminan en cascada (sin órdenes huérfanas).
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // Precisión suficiente para totales de pedido sin float en aplicación.
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Elimina la tabla de pedidos.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
