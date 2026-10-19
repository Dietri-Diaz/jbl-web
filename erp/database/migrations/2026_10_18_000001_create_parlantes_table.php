<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Catálogo de parlantes que muestra la landing.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parlantes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 80);
            $table->string('slug', 90)->unique();
            $table->string('tipo', 40);                       // Portátil, Fiesta, Ultra compacto...
            $table->unsignedSmallInteger('potencia_w');       // vatios
            $table->unsignedSmallInteger('bateria_h');        // horas de batería
            $table->string('resistencia', 10);                // IPX5, IPX7, IP67...
            $table->string('foto')->nullable();               // ruta dentro de public/
            $table->string('foto_alt', 160)->nullable();      // texto alternativo de la foto
            $table->string('sello', 30)->nullable();          // "El más potente": destaca la tarjeta
            $table->boolean('visible')->default(true);        // si se publica en la landing
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parlantes');
    }
};
