<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Catálogo e inventario: precio mostrado y unidades disponibles para validar pedidos.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 10, 2);
            // Entero no negativo coherente con descuentos atómicos en el servicio de pedidos.
            $table->unsignedInteger('stock');
            $table->timestamps();
        });
    }

    /**
     * Elimina la tabla de productos.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
