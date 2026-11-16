<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\Categoria;
use App\Models\Mensaje;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Producto;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PanelController extends Controller
{
    /** Días que cubre el gráfico de ventas. */
    private const DIAS = 14;

    /**
     * Inicio del panel: indicadores, gráficos y lo que espera atención.
     * Un pedido cancelado no cuenta como venta.
     */
    public function inicio()
    {
        $inicioMes = now()->startOfMonth();
        $vendidos = fn () => Pedido::where('estado', '!=', 'cancelado');

        return view('erp.inicio', [
            'ventasMes' => $vendidos()->where('created_at', '>=', $inicioMes)->sum('total'),
            'pedidosMes' => $vendidos()->where('created_at', '>=', $inicioMes)->count(),
            'ventasHoy' => $vendidos()->whereDate('created_at', today())->sum('total'),
            'pendientes' => Pedido::where('estado', 'pendiente')->count(),
            'publicados' => Producto::visibles()->count(),
            'totalProductos' => Producto::count(),
            'stockBajo' => Producto::stockBajo()->count(),
            'noLeidos' => Mensaje::noLeidos()->count(),

            'graficoVentas' => $this->ventasPorDia(),
            'graficoEstados' => $this->pedidosPorEstado(),
            'graficoCategorias' => $this->productosPorCategoria(),

            'ultimosPedidos' => Pedido::latest()->take(6)->get(),
            'productosBajos' => Producto::stockBajo()->orderBy('stock')->take(6)->get(),
            'masVendidos' => $this->masVendidos(),
            'actividad' => Bitacora::latest()->take(7)->get(),
        ]);
    }

    /** Total vendido en cada uno de los últimos días, incluidos los días sin ventas. */
    private function ventasPorDia(): array
    {
        $desde = today()->subDays(self::DIAS - 1);

        $porDia = Pedido::where('estado', '!=', 'cancelado')
            ->where('created_at', '>=', $desde)
            ->get(['total', 'created_at'])
            ->groupBy(fn (Pedido $p) => $p->created_at->toDateString())
            ->map(fn ($pedidos) => round($pedidos->sum('total'), 2));

        $etiquetas = [];
        $valores = [];

        for ($i = 0; $i < self::DIAS; $i++) {
            $dia = $desde->copy()->addDays($i);
            $etiquetas[] = $dia->translatedFormat('d M');
            $valores[] = $porDia->get($dia->toDateString(), 0);
        }

        return ['tipo' => 'linea', 'titulo' => 'Ventas', 'prefijo' => 'S/ ', 'etiquetas' => $etiquetas, 'valores' => $valores];
    }

    private function pedidosPorEstado(): array
    {
        $conteo = Pedido::select('estado', DB::raw('count(*) as total'))->groupBy('estado')->pluck('total', 'estado');
        $estados = config('tienda.estados');

        return [
            'tipo' => 'dona',
            'etiquetas' => array_values($estados),
            'valores' => array_map(fn ($estado) => (int) $conteo->get($estado, 0), array_keys($estados)),
        ];
    }

    private function productosPorCategoria(): array
    {
        $categorias = Categoria::withCount('productos')->orderBy('orden')->get();

        return [
            'tipo' => 'barras',
            'horizontal' => true,
            'titulo' => 'Productos',
            'etiquetas' => $categorias->pluck('nombre')->all(),
            'valores' => $categorias->pluck('productos_count')->all(),
        ];
    }

    /** Los cinco productos con más unidades vendidas (sin contar pedidos cancelados). */
    private function masVendidos()
    {
        return PedidoItem::select('nombre', DB::raw('sum(cantidad) as unidades'), DB::raw('sum(subtotal) as importe'))
            ->whereHas('pedido', fn ($c) => $c->where('estado', '!=', 'cancelado'))
            ->groupBy('nombre')
            ->orderByDesc('unidades')
            ->take(5)
            ->get();
    }
}
