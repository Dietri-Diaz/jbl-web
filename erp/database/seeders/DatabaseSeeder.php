<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Datos iniciales del ERP: un administrador y el catálogo de la landing.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@jbl.pe'],
            ['name' => 'Administrador JBL', 'password' => Hash::make('admin123')],
        );

        $this->call(ParlanteSeeder::class);
    }
}
