<?php

namespace Database\Seeders;

use App\Models\Parlante;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

/**
 * Carga los seis parlantes que ya muestra la landing, con sus mismas fotos.
 * Las fotos se copian desde ../assets/img/productos a public/uploads/parlantes.
 */
class ParlanteSeeder extends Seeder
{
    public function run(): void
    {
        $parlantes = [
            ['JBL Go 2', 'go-2', 'Ultra compacto', 3, 5, 'IPX7', null, 'Parlante JBL Go 2 azul sostenido en la palma de una mano'],
            ['JBL Flip 3', 'flip-3', 'Portátil', 16, 10, 'IPX5', null, 'Parlante JBL Flip 3 turquesa apoyado sobre una mesa de madera'],
            ['JBL Charge 4', 'charge-4', 'Portátil · Powerbank', 30, 20, 'IPX7', null, 'Parlante JBL Charge 4 negro sobre una superficie naranja'],
            ['JBL Xtreme', 'xtreme', 'Portátil con correa', 40, 15, 'IPX5', null, 'Detalle del radiador pasivo lateral de un parlante JBL Xtreme'],
            ['JBL Boombox 2', 'boombox-2', 'Fiesta', 80, 24, 'IPX7', null, 'Parlante JBL Boombox 2 con acabado de camuflaje y asa de transporte'],
            ['JBL Boombox 3', 'boombox-3', 'Fiesta', 180, 24, 'IP67', 'El más potente', 'Parlante JBL Boombox 3 negro sujeto por su asa'],
        ];

        $origen = base_path('../assets/img/productos');
        $destino = public_path('uploads/parlantes');
        File::ensureDirectoryExists($destino);

        foreach ($parlantes as $i => [$nombre, $slug, $tipo, $potencia, $bateria, $resistencia, $sello, $alt]) {
            $foto = null;
            if (File::exists("$origen/$slug.jpg")) {
                File::copy("$origen/$slug.jpg", "$destino/$slug.jpg");
                $foto = "uploads/parlantes/$slug.jpg";
            }

            Parlante::updateOrCreate(['slug' => $slug], [
                'nombre' => $nombre,
                'tipo' => $tipo,
                'potencia_w' => $potencia,
                'bateria_h' => $bateria,
                'resistencia' => $resistencia,
                'foto' => $foto,
                'foto_alt' => $alt,
                'sello' => $sello,
                'visible' => true,
                'orden' => $i + 1,
            ]);
        }
    }
}
