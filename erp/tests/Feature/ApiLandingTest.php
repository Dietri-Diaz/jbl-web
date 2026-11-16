<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Mensaje;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pruebas de la API pública que consume la tienda:
 * GET /api/catalogo, POST /api/pedidos, GET /api/pedidos/{codigo} y POST /api/mensajes.
 * Corren sobre una base SQLite en memoria (ver phpunit.xml).
 */
class ApiLandingTest extends TestCase
{
    use RefreshDatabase;

    private function crearCategoria(array $cambios = []): Categoria
    {
        return Categoria::create(array_merge([
            'nombre' => 'Parlantes portátiles',
            'slug' => 'parlantes-portatiles',
            'visible' => true,
            'orden' => 1,
        ], $cambios));
    }

    /** Crea un producto de prueba; se puede cambiar cualquier campo. */
    private function crearProducto(array $cambios = []): Producto
    {
        return Producto::create(array_merge([
            'categoria_id' => Categoria::first()?->id ?? $this->crearCategoria()->id,
            'nombre' => 'JBL Flip 3',
            'slug' => 'flip-3',
            'tipo' => 'Portátil',
            'precio' => 100,
            'stock' => 10,
            'stock_minimo' => 2,
            'visible' => true,
            'orden' => 1,
        ], $cambios));
    }

    /** Lo que envía el formulario de compra de la tienda. */
    private function datosPedido(array $items, array $cambios = []): array
    {
        return array_merge([
            'nombre' => 'Ana Torres',
            'correo' => 'ana@example.com',
            'telefono' => '987654321',
            'entrega' => 'delivery',
            'direccion' => 'Av. Arequipa 123',
            'distrito' => 'Miraflores',
            'pago' => 'yape',
            'items' => $items,
        ], $cambios);
    }

    // ------------------------------------------------------------------
    // GET /api/catalogo
    // ------------------------------------------------------------------

    public function test_el_catalogo_solo_devuelve_productos_y_categorias_visibles(): void
    {
        $this->crearProducto();
        $this->crearProducto(['nombre' => 'JBL Go 2', 'slug' => 'go-2', 'visible' => false]);

        // Un producto publicado dentro de una categoría oculta tampoco se muestra.
        $oculta = $this->crearCategoria(['nombre' => 'Descontinuados', 'slug' => 'descontinuados', 'visible' => false]);
        $this->crearProducto(['nombre' => 'JBL Clip 2', 'slug' => 'clip-2', 'categoria_id' => $oculta->id]);

        $respuesta = $this->getJson('/api/catalogo');

        $respuesta->assertStatus(200)
            ->assertJsonCount(1, 'productos')
            ->assertJsonPath('productos.0.slug', 'flip-3')
            ->assertJsonCount(1, 'categorias')
            ->assertJsonPath('categorias.0.slug', 'parlantes-portatiles')
            ->assertJsonPath('categorias.0.total', 1)
            ->assertJsonPath('tienda.envio', fn ($envio) => (float) $envio === (float) config('tienda.envio'));
    }

    public function test_el_catalogo_respeta_el_orden_del_panel_y_muestra_la_oferta(): void
    {
        $this->crearProducto(['nombre' => 'JBL Boombox 3', 'slug' => 'boombox-3', 'orden' => 2]);
        $this->crearProducto(['nombre' => 'JBL Go 2', 'slug' => 'go-2', 'orden' => 1, 'precio' => 200, 'precio_oferta' => 150]);

        $respuesta = $this->getJson('/api/catalogo');

        $respuesta->assertJsonPath('productos.0.slug', 'go-2')
            ->assertJsonPath('productos.0.precio_final', 150)
            ->assertJsonPath('productos.0.descuento', 25)
            ->assertJsonPath('productos.1.slug', 'boombox-3')
            ->assertJsonPath('productos.1.precio_oferta', null);
    }

    // ------------------------------------------------------------------
    // POST /api/pedidos
    // ------------------------------------------------------------------

    public function test_un_pedido_valido_se_crea_cobra_envio_y_descuenta_stock(): void
    {
        $producto = $this->crearProducto(['precio' => 100, 'stock' => 10]);

        $respuesta = $this->postJson('/api/pedidos', $this->datosPedido([
            ['id' => $producto->id, 'cantidad' => 2],
        ]));

        $respuesta->assertStatus(201)
            ->assertJsonPath('codigo', 'JBL-000001')
            ->assertJsonPath('estado', 'pendiente')
            ->assertJsonPath('subtotal', 200)
            // json_encode escribe 12.0 como 12: por eso se compara como número.
            ->assertJsonPath('envio', fn ($envio) => (float) $envio === (float) config('tienda.envio'))
            ->assertJsonPath('total', fn ($total) => (float) $total === 200 + (float) config('tienda.envio'))
            ->assertJsonCount(1, 'items');

        $pedido = Pedido::firstOrFail();
        $this->assertSame('JBL-000001', $pedido->codigo);

        // El stock bajó y quedó el movimiento de venta ligado al pedido.
        $this->assertSame(8, $producto->fresh()->stock);
        $this->assertDatabaseHas('movimientos_inventario', [
            'producto_id' => $producto->id,
            'pedido_id' => $pedido->id,
            'tipo' => 'venta',
            'cantidad' => -2,
            'stock_resultante' => 8,
        ]);
    }

    public function test_el_envio_es_gratis_desde_el_monto_configurado_y_al_recoger_en_tienda(): void
    {
        $caro = $this->crearProducto(['precio' => (float) config('tienda.envio_gratis_desde')]);
        $barato = $this->crearProducto(['nombre' => 'JBL Go 2', 'slug' => 'go-2', 'precio' => 50]);

        $this->postJson('/api/pedidos', $this->datosPedido([['id' => $caro->id, 'cantidad' => 1]]))
            ->assertStatus(201)
            ->assertJsonPath('envio', 0);

        $this->postJson('/api/pedidos', $this->datosPedido([['id' => $barato->id, 'cantidad' => 1]], ['entrega' => 'recojo']))
            ->assertStatus(201)
            ->assertJsonPath('envio', 0)
            ->assertJsonPath('total', 50);
    }

    public function test_el_precio_sale_de_la_base_de_datos_y_no_del_cliente(): void
    {
        $producto = $this->crearProducto(['precio' => 200, 'precio_oferta' => 150]);

        // El navegador intenta pagar 1 sol por unidad.
        $respuesta = $this->postJson('/api/pedidos', $this->datosPedido([
            ['id' => $producto->id, 'cantidad' => 2, 'precio' => 1, 'precio_unitario' => 1, 'subtotal' => 2],
        ], ['entrega' => 'recojo', 'total' => 2, 'subtotal' => 2]));

        $respuesta->assertStatus(201)
            ->assertJsonPath('items.0.precio_unitario', 150)
            ->assertJsonPath('subtotal', 300)
            ->assertJsonPath('total', 300);

        $this->assertDatabaseHas('pedido_items', ['producto_id' => $producto->id, 'precio_unitario' => 150, 'subtotal' => 300]);
    }

    public function test_un_pedido_sin_stock_suficiente_se_rechaza(): void
    {
        $producto = $this->crearProducto(['stock' => 1]);

        $respuesta = $this->postJson('/api/pedidos', $this->datosPedido([
            ['id' => $producto->id, 'cantidad' => 2],
        ]));

        $respuesta->assertStatus(422)->assertJsonValidationErrors(['items.0']);

        $this->assertSame(0, Pedido::count());
        $this->assertSame(1, $producto->fresh()->stock);
        $this->assertDatabaseCount('movimientos_inventario', 0);
    }

    public function test_un_producto_oculto_no_se_puede_comprar(): void
    {
        $producto = $this->crearProducto(['visible' => false]);

        $this->postJson('/api/pedidos', $this->datosPedido([['id' => $producto->id, 'cantidad' => 1]]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['items.0']);

        $this->assertSame(0, Pedido::count());
    }

    public function test_un_pedido_incompleto_se_rechaza(): void
    {
        $respuesta = $this->postJson('/api/pedidos', [
            'nombre' => '',
            'correo' => 'no-es-correo',
            'telefono' => '123',
            'entrega' => 'delivery',
            'pago' => 'bitcoin',
            'items' => [],
        ]);

        $respuesta->assertStatus(422)
            ->assertJsonValidationErrors(['nombre', 'correo', 'telefono', 'direccion', 'distrito', 'pago', 'items']);
        $this->assertSame(0, Pedido::count());
    }

    // ------------------------------------------------------------------
    // GET /api/pedidos/{codigo}
    // ------------------------------------------------------------------

    public function test_el_seguimiento_exige_el_correo_del_pedido(): void
    {
        $producto = $this->crearProducto();
        $codigo = $this->postJson('/api/pedidos', $this->datosPedido([['id' => $producto->id, 'cantidad' => 1]]))
            ->assertStatus(201)
            ->json('codigo');

        $this->getJson("/api/pedidos/{$codigo}?correo=ana@example.com")
            ->assertStatus(200)
            ->assertJsonPath('codigo', $codigo)
            ->assertJsonPath('estado_nombre', 'Pendiente')
            ->assertJsonPath('items.0.nombre', 'JBL Flip 3');

        // Con otro correo, sin correo o con un código que no existe: 404.
        $this->getJson("/api/pedidos/{$codigo}?correo=otro@example.com")->assertStatus(404);
        $this->getJson("/api/pedidos/{$codigo}")->assertStatus(404);
        $this->getJson('/api/pedidos/JBL-999999?correo=ana@example.com')->assertStatus(404);
    }

    // ------------------------------------------------------------------
    // POST /api/mensajes
    // ------------------------------------------------------------------

    public function test_un_mensaje_valido_se_guarda(): void
    {
        $respuesta = $this->postJson('/api/mensajes', [
            'nombre' => 'Ana Torres',
            'correo' => 'ana@example.com',
            'mensaje' => 'Quisiera saber dónde comprar el Boombox 3.',
        ]);

        $respuesta->assertStatus(201);
        $this->assertDatabaseHas('mensajes', ['correo' => 'ana@example.com', 'leido' => false]);
    }

    public function test_un_mensaje_incompleto_se_rechaza(): void
    {
        $respuesta = $this->postJson('/api/mensajes', [
            'nombre' => '',
            'correo' => 'esto-no-es-un-correo',
            'mensaje' => 'corto',
        ]);

        $respuesta->assertStatus(422)
            ->assertJsonValidationErrors(['nombre', 'correo', 'mensaje']);
        $this->assertSame(0, Mensaje::count());
    }

    public function test_el_campo_trampa_descarta_los_mensajes_de_bots(): void
    {
        $respuesta = $this->postJson('/api/mensajes', [
            'nombre' => 'Bot',
            'correo' => 'bot@example.com',
            'mensaje' => 'Mensaje automático de publicidad.',
            'sitio_web' => 'http://spam.example.com',
        ]);

        // Se responde 201 para no darle pistas al bot, pero no se guarda nada.
        $respuesta->assertStatus(201);
        $this->assertSame(0, Mensaje::count());
    }
}
