<?php

namespace Database\Seeders;

use App\Models\Bitacora;
use App\Models\Mensaje;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Actividad de ejemplo para que el panel no arranque vacío: pedidos de las
 * últimas dos semanas, mensajes de contacto y registros de bitácora.
 * Todos los nombres y correos son inventados (dominio example.com).
 *
 * Usa una semilla fija: cada vez que se ejecuta genera los mismos datos.
 */
class DemoSeeder extends Seeder
{
    private const CLIENTES = [
        ['Ana Torres Vega', 'ana.torres@example.com', '987654321', 'Av. Arequipa 2450, dpto. 302', 'Lince'],
        ['Luis Paredes Soto', 'luis.paredes@example.com', '912345678', 'Jr. Las Magnolias 118', 'San Borja'],
        ['María Quispe Huamán', 'maria.quispe@example.com', '945112233', 'Av. Los Próceres 760', 'San Juan de Lurigancho'],
        ['Carlos Mendoza Ríos', 'carlos.mendoza@example.com', '998877665', 'Calle Los Cedros 245', 'Santiago de Surco'],
        ['Rosa Flores Chávez', 'rosa.flores@example.com', '934556677', 'Av. Universitaria 1801', 'San Miguel'],
        ['Jorge Salazar León', 'jorge.salazar@example.com', '956778899', 'Av. Larco 1150', 'Miraflores'],
        ['Lucía Ramírez Díaz', 'lucia.ramirez@example.com', '923445566', 'Jr. Huiracocha 2080', 'Jesús María'],
        ['Pedro Castillo Núñez', 'pedro.castillo@example.com', '967889900', 'Av. Túpac Amaru 3290', 'Comas'],
    ];

    public function run(): void
    {
        if (Pedido::exists()) {
            return;   // ya se cargó: no se duplican los pedidos
        }

        mt_srand(2026);

        $admin = User::where('email', 'admin@jbl.pe')->first();
        $editor = User::where('email', 'editor@jbl.pe')->first();

        $this->pedidos($editor ?? $admin);
        $this->mensajes();
        $this->bitacora($admin, $editor);
    }

    /** 22 pedidos repartidos en los últimos 14 días, con su descuento de stock. */
    private function pedidos(?User $atiende): void
    {
        $productos = Producto::visibles()->get();
        $pagos = array_keys(config('tienda.pagos'));

        for ($n = 0; $n < 22; $n++) {
            $cliente = self::CLIENTES[mt_rand(0, count(self::CLIENTES) - 1)];
            $cuando = now()->subDays(13 - intdiv($n * 14, 22))->setTime(mt_rand(9, 21), mt_rand(0, 59));
            $entrega = mt_rand(1, 10) <= 7 ? 'delivery' : 'recojo';

            // Entre 1 y 3 productos distintos con stock.
            $lineas = [];
            foreach ($productos->where('stock', '>', 6)->sortBy(fn () => mt_rand())->take(mt_rand(1, 3)) as $producto) {
                $cantidad = $producto->precioFinal() > 800 ? 1 : mt_rand(1, 2);
                $lineas[] = ['producto' => $producto, 'cantidad' => $cantidad];
            }
            if (! $lineas) {
                continue;
            }

            $subtotal = round(array_sum(array_map(fn ($l) => $l['producto']->precioFinal() * $l['cantidad'], $lineas)), 2);
            $envio = $entrega === 'delivery' && $subtotal < config('tienda.envio_gratis_desde') ? (float) config('tienda.envio') : 0.0;

            // Los pedidos antiguos ya terminaron su recorrido; los recientes siguen en camino.
            $antiguedad = $cuando->diffInDays(now());
            $estado = match (true) {
                $n % 9 === 4 => 'cancelado',
                $antiguedad >= 6 => 'entregado',
                $antiguedad >= 3 => 'enviado',
                $antiguedad >= 1 => 'confirmado',
                default => 'pendiente',
            };

            $pedido = new Pedido([
                'codigo' => 'TMP-'.$n,
                'cliente_nombre' => $cliente[0],
                'cliente_correo' => $cliente[1],
                'cliente_telefono' => $cliente[2],
                'entrega' => $entrega,
                'direccion' => $entrega === 'delivery' ? $cliente[3] : null,
                'distrito' => $entrega === 'delivery' ? $cliente[4] : null,
                'pago' => $pagos[mt_rand(0, count($pagos) - 1)],
                'estado' => $estado,
                'subtotal' => $subtotal,
                'envio' => $envio,
                'total' => round($subtotal + $envio, 2),
            ]);
            $pedido->created_at = $cuando;
            $pedido->updated_at = $cuando;
            $pedido->save();
            $pedido->update(['codigo' => Pedido::codigoPara($pedido->id)]);

            foreach ($lineas as $linea) {
                $producto = $linea['producto'];

                $pedido->items()->create([
                    'producto_id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'precio_unitario' => $producto->precioFinal(),
                    'cantidad' => $linea['cantidad'],
                    'subtotal' => round($producto->precioFinal() * $linea['cantidad'], 2),
                ]);

                $this->fechar($producto->moverStock(-$linea['cantidad'], 'venta', "Pedido {$pedido->codigo}", null, $pedido->id), $cuando);

                // Un pedido cancelado devuelve su stock al día siguiente.
                if ($estado === 'cancelado') {
                    $this->fechar(
                        $producto->moverStock($linea['cantidad'], 'devolucion', "Cancelación del pedido {$pedido->codigo}", $atiende?->id, $pedido->id),
                        $cuando->copy()->addDay(),
                    );
                }
            }

            if ($estado !== 'pendiente') {
                $this->anotar($atiende, 'estado', 'pedidos', "Pasó el pedido {$pedido->codigo} a {$pedido->nombreEstado()}", $cuando->copy()->addHours(3));
            }
        }
    }

    private function mensajes(): void
    {
        $mensajes = [
            ['Ana Torres Vega', 'ana.torres@example.com', 'Hola, quisiera saber si el Boombox 3 viene con cargador incluido y cuánto demora el envío a Lince.', true, 6],
            ['Luis Paredes Soto', 'luis.paredes@example.com', '¿El Charge 4 puede cargar un celular mientras reproduce música? Gracias.', true, 4],
            ['María Quispe Huamán', 'maria.quispe@example.com', 'Me interesa el Flip 3 para la playa. ¿La resistencia IPX5 aguanta la arena y el agua salada?', false, 2],
            ['Jorge Salazar León', 'jorge.salazar@example.com', 'Hice un pedido ayer con recojo en tienda. ¿En qué horario puedo pasar a recogerlo?', false, 1],
            ['Lucía Ramírez Díaz', 'lucia.ramirez@example.com', 'Buenas tardes, ¿emiten factura a nombre de empresa? Necesito tres parlantes para una oficina.', false, 0],
        ];

        foreach ($mensajes as [$nombre, $correo, $texto, $leido, $dias]) {
            $mensaje = new Mensaje(['nombre' => $nombre, 'correo' => $correo, 'mensaje' => $texto, 'leido' => $leido]);
            $mensaje->created_at = now()->subDays($dias)->subHours(mt_rand(1, 8));
            $mensaje->updated_at = $mensaje->created_at;
            $mensaje->save();
        }
    }

    private function bitacora(?User $admin, ?User $editor): void
    {
        $this->anotar($admin, 'crear', 'usuarios', 'Creó la cuenta de Editor de catálogo', now()->subDays(13));
        $this->anotar($admin, 'crear', 'usuarios', 'Creó la cuenta de Consulta', now()->subDays(13));
        $this->anotar($editor, 'ingresar', 'acceso', 'Ingresó al panel', now()->subDays(2)->setTime(9, 12));
        $this->anotar($editor, 'editar', 'productos', 'Actualizó el precio de oferta del JBL Charge 4', now()->subDays(2)->setTime(9, 30));
        $this->anotar($admin, 'ingresar', 'acceso', 'Ingresó al panel', now()->subDay()->setTime(8, 47));
    }

    private function anotar(?User $usuario, string $accion, string $modulo, string $descripcion, Carbon $cuando): void
    {
        $registro = new Bitacora([
            'user_id' => $usuario?->id,
            'usuario' => $usuario?->name ?? 'Tienda',
            'accion' => $accion,
            'modulo' => $modulo,
            'descripcion' => $descripcion,
            'ip' => '127.0.0.1',
        ]);
        $registro->created_at = $cuando;
        $registro->save();
    }

    /** Pone la fecha del pedido al movimiento de inventario que generó. */
    private function fechar($movimiento, Carbon $cuando): void
    {
        $movimiento->created_at = $cuando;
        $movimiento->updated_at = $cuando;
        $movimiento->save();
    }
}
