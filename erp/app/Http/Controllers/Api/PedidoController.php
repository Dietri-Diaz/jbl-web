<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PedidoController extends Controller
{
    /**
     * POST /api/pedidos
     * Registra el pedido del carrito. Los precios y el stock se leen de la
     * base de datos: lo que envía el navegador solo dice qué y cuántos.
     */
    public function store(Request $request)
    {
        $maximo = (int) config('tienda.maximo_por_producto');

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'min:3', 'max:80'],
            'correo' => ['required', 'email', 'max:120'],
            'telefono' => ['required', 'regex:/^9\d{8}$/'],
            'documento' => ['nullable', 'regex:/^(\d{8}|\d{11})$/'],
            'entrega' => ['required', Rule::in(array_keys(config('tienda.entregas')))],
            'direccion' => ['required_if:entrega,delivery', 'nullable', 'string', 'max:160'],
            'distrito' => ['required_if:entrega,delivery', 'nullable', 'string', 'max:60'],
            'referencia' => ['nullable', 'string', 'max:160'],
            'pago' => ['required', Rule::in(array_keys(config('tienda.pagos')))],
            'notas' => ['nullable', 'string', 'max:300'],
            'items' => ['required', 'array', 'min:1', 'max:30'],
            'items.*.id' => ['required', 'integer', 'distinct'],
            'items.*.cantidad' => ['required', 'integer', 'min:1', "max:{$maximo}"],
        ], [
            'telefono.regex' => 'El celular debe tener 9 dígitos y empezar con 9.',
            'documento.regex' => 'El documento debe ser un DNI (8 dígitos) o un RUC (11 dígitos).',
            'direccion.required_if' => 'Escribe la dirección de entrega.',
            'distrito.required_if' => 'Elige el distrito de entrega.',
            'items.required' => 'El carrito está vacío.',
        ]);

        $pedido = DB::transaction(function () use ($datos) {
            // Se bloquean las filas para que dos compras a la vez no vendan la misma unidad.
            // Mismo criterio que el catálogo: producto y categoría publicados.
            $productos = Producto::visibles()
                ->whereHas('categoria', fn ($c) => $c->where('visible', true))
                ->whereIn('id', array_column($datos['items'], 'id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $errores = [];
            $lineas = [];
            $subtotal = 0;

            foreach ($datos['items'] as $i => $item) {
                $producto = $productos->get($item['id']);

                if (! $producto) {
                    $errores["items.$i"] = 'Uno de los productos ya no está disponible.';

                    continue;
                }

                if ($producto->stock < $item['cantidad']) {
                    $errores["items.$i"] = $producto->stock === 0
                        ? "{$producto->nombre} se agotó."
                        : "De {$producto->nombre} solo quedan {$producto->stock} unidades.";

                    continue;
                }

                $precio = $producto->precioFinal();
                $lineas[] = [
                    'producto' => $producto,
                    'cantidad' => $item['cantidad'],
                    'precio' => $precio,
                    'subtotal' => round($precio * $item['cantidad'], 2),
                ];
                $subtotal += $precio * $item['cantidad'];
            }

            if ($errores) {
                throw ValidationException::withMessages($errores);
            }

            $subtotal = round($subtotal, 2);
            $envio = $datos['entrega'] === 'delivery' && $subtotal < config('tienda.envio_gratis_desde')
                ? (float) config('tienda.envio')
                : 0.0;

            $pedido = Pedido::create([
                'codigo' => 'TMP-'.uniqid(),
                'cliente_nombre' => $datos['nombre'],
                'cliente_correo' => $datos['correo'],
                'cliente_telefono' => $datos['telefono'],
                'cliente_documento' => $datos['documento'] ?? null,
                'entrega' => $datos['entrega'],
                'direccion' => $datos['entrega'] === 'delivery' ? $datos['direccion'] : null,
                'distrito' => $datos['entrega'] === 'delivery' ? $datos['distrito'] : null,
                'referencia' => $datos['referencia'] ?? null,
                'pago' => $datos['pago'],
                'estado' => 'pendiente',
                'subtotal' => $subtotal,
                'envio' => $envio,
                'total' => round($subtotal + $envio, 2),
                'notas' => $datos['notas'] ?? null,
            ]);

            // El código definitivo sale del id: JBL-000007.
            $pedido->update(['codigo' => Pedido::codigoPara($pedido->id)]);

            foreach ($lineas as $linea) {
                $pedido->items()->create([
                    'producto_id' => $linea['producto']->id,
                    'nombre' => $linea['producto']->nombre,
                    'precio_unitario' => $linea['precio'],
                    'cantidad' => $linea['cantidad'],
                    'subtotal' => $linea['subtotal'],
                ]);

                $linea['producto']->moverStock(-$linea['cantidad'], 'venta', "Pedido {$pedido->codigo}", null, $pedido->id);
            }

            return $pedido;
        });

        return response()->json($this->resumen($pedido->load('items')), 201);
    }

    /**
     * GET /api/pedidos/{codigo}?correo=...
     * Seguimiento: solo responde si el correo coincide con el del pedido.
     */
    public function show(Request $request, string $codigo)
    {
        // El correo debe llegar como texto (?correo=...); cualquier otra cosa no coincide.
        $correo = $request->query('correo');

        $pedido = is_string($correo) && $correo !== ''
            ? Pedido::with('items')->where('codigo', strtoupper($codigo))->where('cliente_correo', $correo)->first()
            : null;

        if (! $pedido) {
            return response()->json(['mensaje' => 'No encontramos un pedido con ese código y ese correo.'], 404);
        }

        // En el seguimiento no se repite la dirección exacta: basta el distrito.
        return response()->json($this->resumen($pedido, conDireccion: false));
    }

    private function resumen(Pedido $pedido, bool $conDireccion = true): array
    {
        return [
            'codigo' => $pedido->codigo,
            'estado' => $pedido->estado,
            'estado_nombre' => $pedido->nombreEstado(),
            'fecha' => $pedido->created_at->toIso8601String(),
            'cliente' => $pedido->cliente_nombre,
            'entrega' => $pedido->entrega,
            'entrega_nombre' => $pedido->nombreEntrega(),
            'direccion' => $conDireccion ? $pedido->direccion : null,
            'distrito' => $pedido->distrito,
            'pago' => $pedido->pago,
            'pago_nombre' => $pedido->nombrePago(),
            'subtotal' => $pedido->subtotal,
            'envio' => $pedido->envio,
            'total' => $pedido->total,
            'items' => $pedido->items->map(fn ($item) => [
                'nombre' => $item->nombre,
                'cantidad' => $item->cantidad,
                'precio_unitario' => $item->precio_unitario,
                'subtotal' => $item->subtotal,
            ])->all(),
        ];
    }
}
