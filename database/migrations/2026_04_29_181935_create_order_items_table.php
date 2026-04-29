<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Líneas de pedido con snapshot monetario; FK distinta según si borramos pedido o catálogo.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            // Al borrar el pedido, las líneas desaparecen con él.
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            // No borrar productos referenciados en historial: fuerza desactivar/ocultar en catálogo.
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('quantity');
            // Copia del precio al momento de la compra (histórico).
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Elimina la tabla de ítems de pedido.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
