<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pedidos que llegan desde la tienda y el detalle de cada uno.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();              // JBL-000001
            $table->string('cliente_nombre', 80);
            $table->string('cliente_correo', 120)->index();
            $table->string('cliente_telefono', 20);
            $table->string('cliente_documento', 15)->nullable(); // DNI o RUC
            $table->string('entrega', 20);                       // delivery | recojo
            $table->string('direccion', 160)->nullable();
            $table->string('distrito', 60)->nullable();
            $table->string('referencia', 160)->nullable();
            $table->string('pago', 20);                          // contraentrega | yape | transferencia
            $table->string('estado', 20)->default('pendiente')->index();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('envio', 8, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('notas', 300)->nullable();
            $table->timestamps();
        });

        Schema::create('pedido_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->cascadeOnDelete();
            // Si el producto se elimina, el pedido conserva el nombre y el precio.
            $table->foreignId('producto_id')->nullable()->constrained('productos')->nullOnDelete();
            $table->string('nombre', 80);
            $table->decimal('precio_unitario', 8, 2);
            $table->unsignedSmallInteger('cantidad');
            $table->decimal('subtotal', 10, 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedido_items');
        Schema::dropIfExists('pedidos');
    }
};
