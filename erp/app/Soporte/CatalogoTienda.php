<?php

namespace App\Soporte;

use App\Models\Categoria;
use App\Models\Producto;

/**
 * Arma el catálogo que consume la tienda: reglas, categorías y productos.
 * Lo usan la API (GET /api/catalogo) y el comando tienda:exportar, que
 * escribe la copia estática para cuando el ERP no está disponible.
 */
class CatalogoTienda
{
    /**
     * @param  callable|null  $urlFoto  recibe el Producto y devuelve la dirección de su foto
     */
    public static function datos(?callable $urlFoto = null): array
    {
        $urlFoto ??= fn (Producto $p) => $p->urlFoto();

        $productos = Producto::visibles()
            ->with('categoria')
            ->whereHas('categoria', fn ($c) => $c->where('visible', true))
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();

        $categorias = Categoria::visibles()->orderBy('orden')->orderBy('nombre')->get();

        return [
            'tienda' => [
                'moneda' => config('tienda.moneda'),
                'envio' => (float) config('tienda.envio'),
                'envio_gratis_desde' => (float) config('tienda.envio_gratis_desde'),
                'maximo_por_producto' => (int) config('tienda.maximo_por_producto'),
                'entregas' => config('tienda.entregas'),
                'pagos' => config('tienda.pagos'),
                'estados' => config('tienda.estados'),
            ],
            'categorias' => $categorias->map(fn (Categoria $c) => [
                'id' => $c->id,
                'nombre' => $c->nombre,
                'slug' => $c->slug,
                'descripcion' => $c->descripcion,
                'total' => $productos->where('categoria_id', $c->id)->count(),
            ])->filter(fn ($c) => $c['total'] > 0)->values()->all(),
            'productos' => $productos->map(fn (Producto $p) => self::producto($p, $urlFoto))->all(),
        ];
    }

    public static function producto(Producto $p, callable $urlFoto): array
    {
        return [
            'id' => $p->id,
            'sku' => $p->sku,
            'nombre' => $p->nombre,
            'slug' => $p->slug,
            'categoria' => $p->categoria?->slug,
            'categoria_nombre' => $p->categoria?->nombre,
            'tipo' => $p->tipo,
            'descripcion' => $p->descripcion,
            'color' => $p->color,
            'precio' => $p->precio,
            'precio_oferta' => $p->enOferta() ? $p->precio_oferta : null,
            'precio_final' => $p->precioFinal(),
            'descuento' => $p->descuento(),
            'stock' => $p->stock,
            'potencia_w' => $p->potencia_w,
            'bateria_h' => $p->bateria_h,
            'resistencia' => $p->resistencia,
            'sello' => $p->sello,
            'destacado' => $p->destacado,
            'foto' => $urlFoto($p),
            'foto_alt' => $p->foto_alt,
        ];
    }
}
