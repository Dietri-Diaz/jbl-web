<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * La tabla "parlantes" pasa a llamarse "productos" y gana lo que necesita una
 * tienda: categoría, código, precio, oferta, stock y descripción.
 * La ficha técnica deja de ser obligatoria (unos audífonos no tienen vatios).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('parlantes', 'productos');

        Schema::table('productos', function (Blueprint $table) {
            $table->foreignId('categoria_id')->nullable()->after('id')
                ->constrained('categorias')->nullOnDelete();
            $table->string('sku', 30)->nullable()->unique()->after('slug');
            $table->text('descripcion')->nullable()->after('tipo');
            $table->string('color', 30)->nullable()->after('descripcion');
            $table->decimal('precio', 8, 2)->default(0)->after('color');
            $table->decimal('precio_oferta', 8, 2)->nullable()->after('precio');
            $table->unsignedInteger('stock')->default(0)->after('precio_oferta');
            $table->unsignedInteger('stock_minimo')->default(5)->after('stock');
            $table->boolean('destacado')->default(false)->after('sello');
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->unsignedSmallInteger('potencia_w')->nullable()->change();
            $table->unsignedSmallInteger('bateria_h')->nullable()->change();
            $table->string('resistencia', 10)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('categoria_id');
            $table->dropUnique(['sku']);
            $table->dropColumn(['sku', 'descripcion', 'color', 'precio', 'precio_oferta', 'stock', 'stock_minimo', 'destacado']);
        });

        Schema::rename('productos', 'parlantes');
    }
};
