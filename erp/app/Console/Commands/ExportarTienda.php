<?php

namespace App\Console\Commands;

use App\Models\Producto;
use App\Soporte\CatalogoTienda;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Escribe la copia estática del catálogo que usa la tienda cuando el ERP no
 * está disponible (por ejemplo, publicada en Vercel).
 *
 *   php artisan tienda:exportar
 *
 * Conviene ejecutarlo después de cambiar productos o precios y antes de
 * publicar la tienda, para que la copia quede al día.
 */
class ExportarTienda extends Command
{
    protected $signature = 'tienda:exportar';

    protected $description = 'Genera data/catalogo.js, la copia estática del catálogo para la tienda';

    public function handle(): int
    {
        $raiz = base_path('..');

        // En la copia, la foto apunta al archivo del repositorio (assets/img/productos).
        // Una foto subida desde el panel se copia ahí para que también se publique.
        $urlFoto = function (Producto $p) use ($raiz) {
            if (! $p->foto) {
                return null;
            }

            $extension = pathinfo($p->foto, PATHINFO_EXTENSION) ?: 'jpg';
            $relativa = "assets/img/productos/{$p->slug}.{$extension}";
            $origen = public_path($p->foto);

            if (File::exists($origen) && (! File::exists("$raiz/$relativa") || File::hash($origen) !== File::hash("$raiz/$relativa"))) {
                File::ensureDirectoryExists(dirname("$raiz/$relativa"));
                File::copy($origen, "$raiz/$relativa");
            }

            return File::exists("$raiz/$relativa") ? $relativa : null;
        };

        $datos = CatalogoTienda::datos($urlFoto);
        $json = json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $contenido = "// Copia estática del catálogo. NO editar a mano: se genera con\n"
            ."//   php artisan tienda:exportar   (dentro de erp/)\n"
            ."// La tienda la usa cuando el ERP no está disponible.\n"
            ."window.JBL_CATALOGO = {$json};\n";

        File::ensureDirectoryExists("$raiz/data");
        File::put("$raiz/data/catalogo.js", $contenido);

        $this->info(sprintf(
            'data/catalogo.js generado: %d productos en %d categorías.',
            count($datos['productos']),
            count($datos['categorias']),
        ));

        return self::SUCCESS;
    }
}
