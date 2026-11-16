<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\Categoria;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Soporte\Csv;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class InventarioController extends Controller
{
    /** Tipos de movimiento que una persona puede registrar a mano. */
    private const TIPOS_MANUALES = ['entrada', 'salida', 'ajuste'];

    /** Existencias: cuánto hay de cada producto y cuánto vale. */
    public function index(Request $request)
    {
        $buscar = $this->texto($request, 'q');
        $categoria = $this->texto($request, 'categoria');
        $nivel = $this->texto($request, 'nivel', 'todos');

        $productos = Producto::with(['categoria', 'ultimoMovimiento'])
            ->when($buscar !== '', fn ($c) => $c->where(fn ($c) => $c
                ->where('nombre', 'like', "%{$buscar}%")
                ->orWhere('sku', 'like', "%{$buscar}%")))
            ->when(is_numeric($categoria), fn ($c) => $c->where('categoria_id', (int) $categoria))
            // "bajo" incluye los agotados: es todo lo que hay que reponer,
            // igual que el contador del menú y el indicador del inicio.
            ->when($nivel === 'bajo', fn ($c) => $c->stockBajo())
            ->when($nivel === 'agotado', fn ($c) => $c->where('stock', 0))
            ->orderBy('stock')
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('erp.inventario.index', [
            'productos' => $productos,
            'categorias' => Categoria::orderBy('orden')->orderBy('nombre')->get(),
            'filtros' => ['q' => $buscar, 'categoria' => $categoria, 'nivel' => $nivel],
            'hayFiltros' => $buscar !== '' || is_numeric($categoria) || in_array($nivel, ['bajo', 'agotado'], true),

            'unidades' => (int) Producto::sum('stock'),
            'valorInventario' => (float) Producto::selectRaw('sum(precio * stock) as valor')->value('valor'),
            'stockBajo' => Producto::stockBajo()->count(),
            'agotados' => Producto::where('stock', 0)->count(),
        ]);
    }

    /** Historial: cada entrada o salida de stock, la más reciente primero. */
    public function movimientos(Request $request)
    {
        return view('erp.inventario.movimientos', [
            'movimientos' => $this->filtrarMovimientos($request)
                ->with(['producto', 'usuario', 'pedido'])
                ->paginate(20)
                ->withQueryString(),
            'tipos' => MovimientoInventario::TIPOS,
            'productos' => Producto::orderBy('nombre')->get(['id', 'nombre']),
            'filtros' => $request->only(['tipo', 'producto', 'desde', 'hasta']),
            'hayFiltros' => $request->anyFilled(['tipo', 'producto', 'desde', 'hasta']),
        ]);
    }

    /** Descarga en CSV los movimientos que muestra el historial con sus filtros. */
    public function exportar(Request $request)
    {
        $filas = $this->filtrarMovimientos($request)
            ->with(['producto', 'usuario', 'pedido'])
            ->get()
            ->map(fn (MovimientoInventario $m) => [
                $m->created_at->format('d/m/Y H:i'),
                $m->producto?->nombre,
                $m->producto?->sku,
                $m->nombreTipo(),
                $m->cantidad,
                $m->stock_resultante,
                $m->motivo,
                $m->usuario?->name ?? 'Tienda',
                $m->pedido?->codigo,
            ]);

        return Csv::descargar(
            'movimientos-'.now()->format('Y-m-d').'.csv',
            ['Fecha', 'Producto', 'SKU', 'Tipo', 'Cantidad', 'Stock resultante', 'Motivo', 'Usuario', 'Pedido'],
            $filas,
        );
    }

    /** Formulario para registrar una entrada, una salida o un ajuste. */
    public function ajustar(Producto $producto)
    {
        return view('erp.inventario.ajustar', [
            'producto' => $producto->load('categoria'),
            'ultimos' => $producto->movimientos()->with(['usuario', 'pedido'])->latest()->latest('id')->take(8)->get(),
        ]);
    }

    public function guardar(Request $request, Producto $producto)
    {
        $datos = $request->validate([
            'tipo' => ['required', Rule::in(self::TIPOS_MANUALES)],
            // En un ajuste la cantidad es el stock final, que sí puede ser 0.
            'cantidad' => ['required', 'integer', $request->input('tipo') === 'ajuste' ? 'min:0' : 'min:1', 'max:99999'],
            'motivo' => ['required', 'string', 'max:160'],
        ], [
            'tipo.required' => 'Elige el tipo de movimiento.',
            'motivo.required' => 'Escribe el motivo: queda en el historial.',
        ]);

        $cantidad = (int) $datos['cantidad'];

        $movimiento = DB::transaction(function () use ($producto, $datos, $cantidad, $request) {
            // Se bloquea la fila para que una venta simultánea no desordene el stock.
            $producto = Producto::lockForUpdate()->findOrFail($producto->id);

            // Cuánto sube o baja el stock según el tipo de movimiento.
            $cambio = match ($datos['tipo']) {
                'entrada' => $cantidad,
                'salida' => -$cantidad,
                'ajuste' => $cantidad - $producto->stock,
            };

            if ($datos['tipo'] === 'salida' && $cantidad > $producto->stock) {
                throw ValidationException::withMessages([
                    'cantidad' => "No puedes sacar {$cantidad}: solo hay {$producto->stock} en stock.",
                ]);
            }

            if ($datos['tipo'] === 'ajuste' && $cambio === 0) {
                throw ValidationException::withMessages([
                    'cantidad' => "El stock ya es {$producto->stock}: no hay nada que ajustar.",
                ]);
            }

            return $producto->moverStock($cambio, $datos['tipo'], $datos['motivo'], $request->user()->id);
        });

        $signo = $movimiento->cantidad > 0 ? '+' : '';
        Bitacora::registrar('stock', 'inventario', "{$movimiento->nombreTipo()} de {$producto->nombre}: {$signo}{$movimiento->cantidad} (stock {$movimiento->stock_resultante})");

        return redirect()->route('inventario.index')
            ->with('ok', "Stock de {$producto->nombre} actualizado: ahora hay {$movimiento->stock_resultante}.");
    }

    private function filtrarMovimientos(Request $request): Builder
    {
        $tipo = $this->texto($request, 'tipo');
        $producto = $this->texto($request, 'producto');
        $desde = $this->fecha($request, 'desde');
        $hasta = $this->fecha($request, 'hasta');

        return MovimientoInventario::query()
            ->when(array_key_exists((string) $tipo, MovimientoInventario::TIPOS), fn ($c) => $c->where('tipo', $tipo))
            ->when(is_numeric($producto), fn ($c) => $c->where('producto_id', (int) $producto))
            ->when($desde, fn ($c) => $c->where('created_at', '>=', $desde))
            ->when($hasta, fn ($c) => $c->where('created_at', '<=', $hasta->endOfDay()))
            ->latest()
            ->latest('id');
    }
}
