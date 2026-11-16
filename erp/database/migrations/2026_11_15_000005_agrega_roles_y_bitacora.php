<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Roles de los usuarios del panel y bitácora de lo que hace cada uno.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('rol', 20)->default('admin')->after('password');   // admin | editor | lector
            $table->boolean('activo')->default(true)->after('rol');
            $table->timestamp('ultimo_ingreso')->nullable()->after('activo');
        });

        Schema::create('bitacora', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('usuario', 80);           // nombre al momento de la acción
            $table->string('accion', 20)->index();   // crear | editar | eliminar | ingresar | salir | estado | stock
            $table->string('modulo', 30)->index();   // productos, pedidos, usuarios...
            $table->string('descripcion', 255);
            $table->string('ip', 45)->nullable();
            $table->timestamp('created_at')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bitacora');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['rol', 'activo', 'ultimo_ingreso']);
        });
    }
};
