<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductoRequest;
use App\Models\Bitacora;
use App\Models\Categoria;
use App\Models\PedidoItem;
use App\Models\Producto;
use App\Soporte\Csv;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProductoController extends Controller
{
    /** Carpeta (dentro de public/) donde se guardan las fotos. */
    private const CARPETA_FOTOS = 'uploads/productos';

    /** Formas de ordenar el listado y el texto que ve el usuario. */
    public const ORDENES = [
        'orden' => 'Orden de la tienda',
        'nombre' => 'Nombre (A-Z)',
        'precio-menor' => 'Precio: menor a mayor',
        'precio-mayor' => 'Precio: mayor a menor',
        'stock-menor' => 'Stock: menor a mayor',
        'recientes' => 'Más recientes',
    ];

    public function index(Request $request)
    {
        return view('erp.productos.index', [
            'productos' => $this->filtrar($request)->with('categoria')->paginate(12)->withQueryString(),
            'categorias' => Categoria::orderBy('orden')->orderBy('nombre')->get(),
            'ordenes' => self::ORDENES,
            'filtros' => $request->only(['q', 'categoria', 'estado', 'stock', 'orden']),
            'hayFiltros' => $request->anyFilled(['q', 'categoria', 'estado', 'stock']),

            // Indicadores de todo el catálogo (no dependen de los filtros).
            'total' => Producto::count(),
            'publicados' => Producto::visibles()->count(),
            'enOferta' => Producto::whereNotNull('precio_oferta')
                ->where('precio_oferta', '>', 0)
                ->whereColumn('precio_oferta', '<', 'precio')
                ->count(),
            'agotados' => Producto::where('stock', 0)->count(),
            'valorInventario' => (float) Producto::selectRaw('sum(precio * stock) as valor')->value('valor'),
        ]);
    }

    /** Descarga en CSV los mismos productos que muestra el listado con sus filtros. */
    public function exportar(Request $request)
    {
        $filas = $this->filtrar($request)->with('categoria')->get()->map(fn (Producto $p) => [
            $p->sku,
            $p->nombre,
            $p->categoria?->nombre,
            $p->tipo,
            number_format($p->precio, 2, '.', ''),
            $p->enOferta() ? number_format($p->precio_oferta, 2, '.', '') : '',
            $p->stock,
            $p->stock_minimo,
            $p->visible ? 'Publicado' : 'Oculto',
            $p->destacado ? 'Sí' : 'No',
        ]);

        return Csv::descargar(
            'productos-'.now()->format('Y-m-d').'.csv',
            ['SKU', 'Nombre', 'Categoría', 'Tipo', 'Precio', 'Precio de oferta', 'Stock', 'Stock mínimo', 'Estado', 'Destacado'],
            $filas,
        );
    }

    public function create()
    {
        return view('erp.productos.crear', [
            'producto' => new Producto([
                'visible' => true,
                'destacado' => false,
                'stock_minimo' => 5,
                'orden' => Producto::max('orden') + 1,
            ]),
            'categorias' => Categoria::orderBy('orden')->orderBy('nombre')->get(),
            'resistencias' => ProductoRequest::RESISTENCIAS,
        ]);
    }

    public function store(ProductoRequest $request)
    {
        $datos = $request->safe()->except(['foto', 'stock_inicial']);
        $datos['slug'] = $this->slugUnico($datos['nombre']);
        $datos['visible'] = $request->boolean('visible');
        $datos['destacado'] = $request->boolean('destacado');
        $datos['orden'] ??= Producto::max('orden') + 1;
        $datos['stock'] = 0;
        $datos['foto'] = $this->guardarFoto($request->file('foto'), $datos['slug']);

        $inicial = (int) $request->input('stock_inicial', 0);

        // El producto y su primer movimiento se guardan juntos o no se guarda nada.
        $producto = DB::transaction(function () use ($datos, $inicial, $request) {
            $producto = Producto::create($datos);

            // El stock nunca se escribe directo: entra como un movimiento del inventario.
            if ($inicial > 0) {
                $producto->moverStock($inicial, 'entrada', 'Stock inicial', $request->user()->id);
            }

            return $producto;
        });

        Bitacora::registrar('crear', 'productos', "Agregó el producto {$producto->nombre}");

        return redirect()->route('productos.index')
            ->with('ok', "Se agregó {$producto->nombre} al catálogo.");
    }

    public function edit(Producto $producto)
    {
        return view('erp.productos.editar', [
            'producto' => $producto,
            'categorias' => Categoria::orderBy('orden')->orderBy('nombre')->get(),
            'resistencias' => ProductoRequest::RESISTENCIAS,
        ]);
    }

    public function update(ProductoRequest $request, Producto $producto)
    {
        $datos = $request->safe()->except('foto');
        $datos['visible'] = $request->boolean('visible');
        $datos['destacado'] = $request->boolean('destacado');
        $datos['orden'] ??= $producto->orden;

        // El slug no cambia al editar: los enlaces de la tienda lo usan
        // (producto.html?p=flip-5) y dejarían de funcionar.

        if ($request->hasFile('foto')) {
            $this->borrarFoto($producto->foto);
            $datos['foto'] = $this->guardarFoto($request->file('foto'), $datos['slug'] ?? $producto->slug);
        }

        $producto->update($datos);

        Bitacora::registrar('editar', 'productos', "Editó el producto {$producto->nombre}");

        return redirect()->route('productos.index')
            ->with('ok', "Se guardaron los cambios de {$producto->nombre}.");
    }

    public function destroy(Producto $producto)
    {
        // Un producto vendido se conserva para no perder el detalle de esos pedidos.
        if (PedidoItem::where('producto_id', $producto->id)->exists()) {
            return back()->with('error', "{$producto->nombre} aparece en pedidos y no se puede eliminar. Puedes ocultarlo para que no se venda.");
        }

        $this->borrarFoto($producto->foto);
        $producto->delete();

        Bitacora::registrar('eliminar', 'productos', "Eliminó el producto {$producto->nombre}");

        return redirect()->route('productos.index')
            ->with('ok', "Se eliminó {$producto->nombre} del catálogo.");
    }

    /** Publica u oculta el producto en la tienda sin entrar a editarlo. */
    public function alternarVisible(Producto $producto)
    {
        $producto->update(['visible' => ! $producto->visible]);

        Bitacora::registrar('estado', 'productos', $producto->visible
            ? "Publicó el producto {$producto->nombre}"
            : "Ocultó el producto {$producto->nombre}");

        return back()->with('ok', $producto->visible
            ? "{$producto->nombre} ya se muestra en la tienda."
            : "{$producto->nombre} quedó oculto en la tienda.");
    }

    /** Arma la consulta del listado con los filtros de la dirección (?q=...&stock=...). */
    private function filtrar(Request $request): Builder
    {
        $buscar = $this->texto($request, 'q');
        $categoria = $this->texto($request, 'categoria');
        $estado = $this->texto($request, 'estado');
        $stock = $this->texto($request, 'stock');

        $consulta = Producto::query()
            ->when($buscar !== '', fn ($c) => $c->where(fn ($c) => $c
                ->where('nombre', 'like', "%{$buscar}%")
                ->orWhere('sku', 'like', "%{$buscar}%")
                ->orWhere('tipo', 'like', "%{$buscar}%")))
            ->when(is_numeric($categoria), fn ($c) => $c->where('categoria_id', (int) $categoria))
            ->when($estado === 'publicados', fn ($c) => $c->where('visible', true))
            ->when($estado === 'ocultos', fn ($c) => $c->where('visible', false))
            // Los tres niveles coinciden con Producto::estadoStock().
            ->when($stock === 'disponible', fn ($c) => $c->whereColumn('stock', '>', 'stock_minimo'))
            ->when($stock === 'bajo', fn ($c) => $c->where('stock', '>', 0)->whereColumn('stock', '<=', 'stock_minimo'))
            ->when($stock === 'agotado', fn ($c) => $c->where('stock', 0));

        return match ($this->texto($request, 'orden')) {
            'nombre' => $consulta->orderBy('nombre'),
            'precio-menor' => $consulta->orderBy('precio'),
            'precio-mayor' => $consulta->orderByDesc('precio'),
            'stock-menor' => $consulta->orderBy('stock')->orderBy('nombre'),
            'recientes' => $consulta->latest(),
            default => $consulta->orderBy('orden')->orderBy('nombre'),
        };
    }

    /** "JBL Flip 3" -> "jbl-flip-3"; si ya existe, "jbl-flip-3-2". */
    private function slugUnico(string $nombre, ?int $ignorarId = null): string
    {
        $base = Str::slug($nombre) ?: 'producto';
        $slug = $base;
        $n = 2;

        while (Producto::where('slug', $slug)->when($ignorarId, fn ($c) => $c->where('id', '!=', $ignorarId))->exists()) {
            $slug = "{$base}-{$n}";
            $n++;
        }

        return $slug;
    }

    private function guardarFoto(UploadedFile $foto, string $slug): string
    {
        $nombre = $slug.'-'.now()->format('YmdHis').'.'.$foto->extension();
        $foto->move(public_path(self::CARPETA_FOTOS), $nombre);

        return self::CARPETA_FOTOS.'/'.$nombre;
    }

    private function borrarFoto(?string $ruta): void
    {
        // Solo se borran archivos de la carpeta de fotos, nunca otra ruta.
        if ($ruta && str_starts_with($ruta, self::CARPETA_FOTOS.'/') && ! str_contains($ruta, '..')) {
            File::delete(public_path($ruta));
        }
    }
}
