<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\Pedido;
use App\Models\Producto;
use App\Soporte\Csv;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PedidoController extends Controller
{
    public function index(Request $request)
    {
        $estado = $this->estadoElegido($request);

        // Conteo por estado para las pestañas (respeta búsqueda y fechas).
        $conteos = $this->filtrar($request)
            ->select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado');

        // Un pedido cancelado no cuenta como venta.
        $vendido = (float) $this->filtrar($request)->where('estado', '!=', 'cancelado')->sum('total');
        $validos = $this->filtrar($request)->where('estado', '!=', 'cancelado')->count();

        return view('erp.pedidos.index', [
            'pedidos' => $this->filtrar($request, $estado)->latest()->latest('id')->paginate(15)->withQueryString(),
            'estado' => $estado ?? 'todos',
            'estados' => config('tienda.estados'),
            'conteos' => $conteos,
            'filtros' => $request->only(['q', 'desde', 'hasta']),
            'hayFiltros' => $request->anyFilled(['q', 'desde', 'hasta']),

            'totalPedidos' => (int) $conteos->sum(),
            'vendido' => $vendido,
            'ticketPromedio' => $validos > 0 ? $vendido / $validos : 0,
            'pendientes' => (int) $conteos->get('pendiente', 0),
        ]);
    }

    /** Descarga en CSV los pedidos que muestra el listado con sus filtros. */
    public function exportar(Request $request)
    {
        $filas = $this->filtrar($request, $this->estadoElegido($request))
            ->latest()
            ->latest('id')
            ->get()
            ->map(fn (Pedido $p) => [
                $p->codigo,
                $p->created_at->format('d/m/Y H:i'),
                $p->cliente_nombre,
                $p->cliente_correo,
                $p->cliente_telefono,
                $p->cliente_documento,
                $p->nombreEntrega(),
                $p->distrito,
                $p->nombrePago(),
                $p->nombreEstado(),
                number_format($p->subtotal, 2, '.', ''),
                number_format($p->envio, 2, '.', ''),
                number_format($p->total, 2, '.', ''),
            ]);

        return Csv::descargar(
            'pedidos-'.now()->format('Y-m-d').'.csv',
            ['Código', 'Fecha', 'Cliente', 'Correo', 'Teléfono', 'Documento', 'Entrega', 'Distrito', 'Pago', 'Estado', 'Subtotal', 'Envío', 'Total'],
            $filas,
        );
    }

    public function show(Pedido $pedido)
    {
        $pedido->load(['items.producto', 'movimientos.producto', 'movimientos.usuario']);

        return view('erp.pedidos.ver', [
            'pedido' => $pedido,
            'historial' => $this->historial($pedido),
        ]);
    }

    /** Pasa el pedido al estado elegido si la transición está permitida. */
    public function cambiarEstado(Request $request, Pedido $pedido)
    {
        $datos = $request->validate([
            'estado' => ['required', Rule::in(array_keys(config('tienda.estados')))],
        ]);

        $nuevo = $datos['estado'];
        $anterior = $pedido->nombreEstado();

        // El estado y la devolución del stock se guardan juntos o no se guarda nada.
        // Se bloquea el pedido para que dos personas no lo cambien a la vez
        // (dos cancelaciones simultáneas devolverían el stock dos veces).
        $cambio = DB::transaction(function () use ($pedido, $nuevo, $request) {
            $actual = Pedido::lockForUpdate()->findOrFail($pedido->id);

            if (! $actual->puedePasarA($nuevo)) {
                return false;
            }

            $actual->update(['estado' => $nuevo]);

            if ($nuevo === 'cancelado') {
                // Lo que se vendió vuelve al inventario (si el producto todavía existe).
                foreach ($actual->items as $item) {
                    $producto = $item->producto_id ? Producto::lockForUpdate()->find($item->producto_id) : null;

                    $producto?->moverStock(
                        $item->cantidad,
                        'devolucion',
                        "Cancelación del pedido {$actual->codigo}",
                        $request->user()->id,
                        $actual->id,
                    );
                }
            }

            return true;
        });

        $pedido->refresh();

        if (! $cambio) {
            return back()->with('error', "El pedido {$pedido->codigo} está {$pedido->nombreEstado()} y no puede pasar a ".config("tienda.estados.{$nuevo}").'.');
        }

        Bitacora::registrar('estado', 'pedidos', "Pasó el pedido {$pedido->codigo} de {$anterior} a {$pedido->nombreEstado()}");

        return redirect()->route('pedidos.show', $pedido)
            ->with('ok', $nuevo === 'cancelado'
                ? "Pedido {$pedido->codigo} cancelado. El stock volvió al inventario."
                : "El pedido {$pedido->codigo} ahora está {$pedido->nombreEstado()}.");
    }

    /** Estado de la pestaña elegida, o null si es "Todos" (o un valor desconocido). */
    private function estadoElegido(Request $request): ?string
    {
        $estado = $this->texto($request, 'estado');

        return array_key_exists($estado, config('tienda.estados')) ? $estado : null;
    }

    /** Consulta con la búsqueda y el rango de fechas; el estado es opcional. */
    private function filtrar(Request $request, ?string $estado = null): Builder
    {
        $buscar = $this->texto($request, 'q');
        $desde = $this->fecha($request, 'desde');
        $hasta = $this->fecha($request, 'hasta');

        return Pedido::query()
            ->when($buscar !== '', fn ($c) => $c->where(fn ($c) => $c
                ->where('codigo', 'like', "%{$buscar}%")
                ->orWhere('cliente_nombre', 'like', "%{$buscar}%")
                ->orWhere('cliente_correo', 'like', "%{$buscar}%")))
            ->when($desde, fn ($c) => $c->where('created_at', '>=', $desde))
            ->when($hasta, fn ($c) => $c->where('created_at', '<=', $hasta->endOfDay()))
            ->when($estado, fn ($c) => $c->where('estado', $estado));
    }

    /**
     * Línea de tiempo del pedido: cuándo llegó, los cambios de estado que
     * quedaron en la bitácora y sus movimientos de inventario.
     */
    private function historial(Pedido $pedido)
    {
        $llegada = collect([[
            'fecha' => $pedido->created_at,
            'texto' => 'El cliente hizo el pedido en la tienda',
            'autor' => 'Tienda',
        ]]);

        $cambios = Bitacora::where('modulo', 'pedidos')
            ->where('descripcion', 'like', "%{$pedido->codigo}%")
            ->get()
            ->map(fn (Bitacora $registro) => [
                'fecha' => $registro->created_at,
                'texto' => $registro->descripcion,
                'autor' => $registro->usuario,
            ]);

        $movimientos = $pedido->movimientos->map(fn ($m) => [
            'fecha' => $m->created_at,
            'texto' => $m->nombreTipo().': '.($m->cantidad > 0 ? '+' : '').$m->cantidad.' de '.($m->producto?->nombre ?? 'un producto eliminado'),
            'autor' => $m->usuario?->name ?? 'Tienda',
        ]);

        return $llegada->concat($cambios)->concat($movimientos)->sortBy('fecha')->values();
    }
}
