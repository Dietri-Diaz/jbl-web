<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

/**
 * Carga las categorías y los productos de la tienda desde
 * database/seeders/datos/catalogo.php.
 *
 * Las fotos se copian de ../assets/img/productos (las que usa la tienda
 * estática) a public/uploads/productos (las que sirve el ERP).
 * Los precios y el stock son datos de ejemplo para la demostración.
 */
class CatalogoSeeder extends Seeder
{
    public function run(): void
    {
        $datos = require __DIR__.'/datos/catalogo.php';

        $origen = base_path('../assets/img/productos');
        $destino = public_path('uploads/productos');
        File::ensureDirectoryExists($destino);

        $orden = 0;

        foreach ($datos as $i => $grupo) {
            $categoria = Categoria::updateOrCreate(['slug' => $grupo['slug']], [
                'nombre' => $grupo['nombre'],
                'descripcion' => $grupo['descripcion'],
                'visible' => true,
                'orden' => $i + 1,
            ]);

            foreach ($grupo['productos'] as $p) {
                $foto = null;
                if (File::exists("$origen/{$p['slug']}.jpg")) {
                    File::copy("$origen/{$p['slug']}.jpg", "$destino/{$p['slug']}.jpg");
                    $foto = "uploads/productos/{$p['slug']}.jpg";
                }

                $producto = Producto::updateOrCreate(['slug' => $p['slug']], [
                    'categoria_id' => $categoria->id,
                    'nombre' => $p['nombre'],
                    'sku' => $p['sku'],
                    'tipo' => $p['tipo'],
                    'descripcion' => $p['descripcion'],
                    'color' => $p['color'] ?? null,
                    'precio' => $p['precio'],
                    'precio_oferta' => $p['oferta'] ?? null,
                    'stock_minimo' => $p['minimo'] ?? 5,
                    'potencia_w' => $p['potencia'] ?? null,
                    'bateria_h' => $p['bateria'] ?? null,
                    'resistencia' => $p['resistencia'] ?? null,
                    'foto' => $foto,
                    'foto_alt' => $p['alt'],
                    'sello' => $p['sello'] ?? null,
                    'destacado' => $p['destacado'] ?? false,
                    'visible' => true,
                    'orden' => ++$orden,
                ]);

                // El stock entra como un movimiento, para que el historial empiece completo.
                if ($p['stock'] > 0 && ! $producto->movimientos()->exists()) {
                    $producto->moverStock($p['stock'], 'entrada', 'Stock inicial');
                }
            }
        }
    }
}
