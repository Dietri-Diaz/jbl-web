<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Datos iniciales del ERP: un usuario por cada rol, el catálogo de la
     * tienda y actividad de ejemplo (pedidos, mensajes y bitácora).
     *
     * La contraseña de las tres cuentas es "admin123": hay que cambiarla
     * antes de usar el sistema fuera de una demostración.
     */
    public function run(): void
    {
        $cuentas = [
            ['admin@jbl.pe', 'Administrador JBL', 'admin'],
            ['editor@jbl.pe', 'Editor de catálogo', 'editor'],
            ['consulta@jbl.pe', 'Consulta', 'lector'],
        ];

        foreach ($cuentas as [$correo, $nombre, $rol]) {
            User::updateOrCreate(
                ['email' => $correo],
                ['name' => $nombre, 'password' => 'admin123', 'rol' => $rol, 'activo' => true],
            );
        }

        $this->call([
            CatalogoSeeder::class,
            DemoSeeder::class,
        ]);
    }
}
