<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $this->texto($request, 'q');
        $estado = $this->texto($request, 'estado');

        $categorias = Categoria::withCount('productos')
            ->when($buscar !== '', fn ($c) => $c->where(fn ($c) => $c
                ->where('nombre', 'like', "%{$buscar}%")
                ->orWhere('descripcion', 'like', "%{$buscar}%")))
            ->when($estado === 'visibles', fn ($c) => $c->where('visible', true))
            ->when($estado === 'ocultas', fn ($c) => $c->where('visible', false))
            ->orderBy('orden')
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('erp.categorias.index', [
            'categorias' => $categorias,
            'filtros' => $request->only(['q', 'estado']),
            'hayFiltros' => $request->anyFilled(['q', 'estado']),
            'total' => Categoria::count(),
            'visibles' => Categoria::visibles()->count(),
            'sinCategoria' => Producto::whereNull('categoria_id')->count(),
        ]);
    }

    public function create()
    {
        return view('erp.categorias.crear', [
            'categoria' => new Categoria(['visible' => true, 'orden' => Categoria::max('orden') + 1]),
        ]);
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);
        $datos['slug'] = $this->slugUnico($datos['nombre']);
        $datos['visible'] = $request->boolean('visible');
        $datos['orden'] ??= Categoria::max('orden') + 1;

        $categoria = Categoria::create($datos);

        Bitacora::registrar('crear', 'categorias', "Creó la categoría {$categoria->nombre}");

        return redirect()->route('categorias.index')
            ->with('ok', "Se creó la categoría {$categoria->nombre}.");
    }

    public function edit(Categoria $categoria)
    {
        return view('erp.categorias.editar', [
            'categoria' => $categoria->loadCount('productos'),
        ]);
    }

    public function update(Request $request, Categoria $categoria)
    {
        $datos = $this->validar($request);
        $datos['visible'] = $request->boolean('visible');
        $datos['orden'] ??= $categoria->orden;

        // El slug no cambia al editar: los enlaces de la tienda lo usan
        // (catalogo.html?categoria=audifonos) y dejarían de funcionar.

        $categoria->update($datos);

        Bitacora::registrar('editar', 'categorias', "Editó la categoría {$categoria->nombre}");

        return redirect()->route('categorias.index')
            ->with('ok', "Se guardaron los cambios de {$categoria->nombre}.");
    }

    public function destroy(Categoria $categoria)
    {
        // Primero hay que mover sus productos a otra categoría.
        $productos = $categoria->productos()->count();

        if ($productos > 0) {
            return back()->with('error', "{$categoria->nombre} tiene {$productos} ".($productos === 1 ? 'producto' : 'productos').'. Muévelos a otra categoría antes de eliminarla.');
        }

        $categoria->delete();

        Bitacora::registrar('eliminar', 'categorias', "Eliminó la categoría {$categoria->nombre}");

        return redirect()->route('categorias.index')
            ->with('ok', "Se eliminó la categoría {$categoria->nombre}.");
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:60'],
            'descripcion' => ['nullable', 'string', 'max:200'],
            'visible' => ['nullable', 'boolean'],
            'orden' => ['nullable', 'integer', 'min:0', 'max:999'],
        ]);
    }

    /** "Audio para casa" -> "audio-para-casa"; si ya existe, "audio-para-casa-2". */
    private function slugUnico(string $nombre, ?int $ignorarId = null): string
    {
        $base = Str::slug($nombre) ?: 'categoria';
        $slug = $base;
        $n = 2;

        while (Categoria::where('slug', $slug)->when($ignorarId, fn ($c) => $c->where('id', '!=', $ignorarId))->exists()) {
            $slug = "{$base}-{$n}";
            $n++;
        }

        return $slug;
    }
}
