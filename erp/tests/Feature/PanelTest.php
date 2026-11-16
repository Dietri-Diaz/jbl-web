<?php

namespace Tests\Feature;

use App\Models\Bitacora;
use App\Models\Categoria;
use App\Models\Mensaje;
use App\Models\MovimientoInventario;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Pruebas del panel de administración (todo lo que está bajo /panel).
 * Corren sobre una base SQLite en memoria (ver phpunit.xml): no tocan MySQL.
 */
class PanelTest extends TestCase
{
    use RefreshDatabase;

    /** Fotos que ya existían antes de la prueba, para borrar solo las nuevas. */
    private ?array $fotosAntes = null;

    protected function tearDown(): void
    {
        // Si una prueba subió una foto, se borra para no ensuciar public/.
        if ($this->fotosAntes !== null) {
            foreach (array_diff($this->fotosSubidas(), $this->fotosAntes) as $foto) {
                File::delete($foto);
            }
        }

        parent::tearDown();
    }

    // ------------------------------------------------------------------
    // Datos de ejemplo
    // ------------------------------------------------------------------

    /** Solo las fotos del producto que crea la prueba ("JBL Flip 6"), no las del catálogo real. */
    private function fotosSubidas(): array
    {
        return glob(public_path('uploads/productos/jbl-flip-6-*')) ?: [];
    }

    private function usuario(string $rol = 'admin', array $cambios = []): User
    {
        return User::factory()->create(array_merge(['rol' => $rol, 'activo' => true], $cambios));
    }

    private function categoria(array $cambios = []): Categoria
    {
        $n = Categoria::count() + 1;

        return Categoria::create(array_merge([
            'nombre' => "Categoría {$n}",
            'slug' => "categoria-{$n}",
            'visible' => true,
            'orden' => $n,
        ], $cambios));
    }

    private function producto(array $cambios = []): Producto
    {
        $n = Producto::count() + 1;

        return Producto::create(array_merge([
            'categoria_id' => Categoria::first()?->id ?? $this->categoria()->id,
            'nombre' => "JBL Prueba {$n}",
            'slug' => "jbl-prueba-{$n}",
            'sku' => "SKU-{$n}",
            'tipo' => 'Portátil',
            'precio' => 100,
            'stock' => 10,
            'stock_minimo' => 3,
            'foto_alt' => 'Parlante de prueba',
            'visible' => true,
            'orden' => $n,
        ], $cambios));
    }

    /** Pedido con un solo producto; descuenta el stock igual que la tienda. */
    private function pedido(Producto $producto, int $cantidad = 2, string $estado = 'pendiente'): Pedido
    {
        $subtotal = $producto->precioFinal() * $cantidad;

        $pedido = Pedido::create([
            'codigo' => 'TMP-'.uniqid(),
            'cliente_nombre' => 'Ana Torres',
            'cliente_correo' => 'ana@example.com',
            'cliente_telefono' => '987654321',
            'entrega' => 'recojo',
            'pago' => 'yape',
            'estado' => $estado,
            'subtotal' => $subtotal,
            'envio' => 0,
            'total' => $subtotal,
        ]);
        $pedido->update(['codigo' => Pedido::codigoPara($pedido->id)]);

        $pedido->items()->create([
            'producto_id' => $producto->id,
            'nombre' => $producto->nombre,
            'precio_unitario' => $producto->precioFinal(),
            'cantidad' => $cantidad,
            'subtotal' => $subtotal,
        ]);

        $producto->moverStock(-$cantidad, 'venta', "Pedido {$pedido->codigo}", null, $pedido->id);

        return $pedido;
    }

    /** Campos válidos del formulario de producto. */
    private function datosProducto(array $cambios = []): array
    {
        return array_merge([
            'nombre' => 'JBL Flip 6',
            'categoria_id' => Categoria::first()?->id ?? $this->categoria()->id,
            'tipo' => 'Portátil',
            'sku' => 'JBL-FLIP6',
            'precio' => '499.90',
            'precio_oferta' => '449.90',
            'potencia_w' => 30,
            'bateria_h' => 12,
            'resistencia' => 'IP67',
            'foto_alt' => 'Parlante JBL Flip 6 negro',
            'visible' => '1',
            'destacado' => '0',
            'stock_inicial' => 7,
            'stock_minimo' => 2,
        ], $cambios);
    }

    // ------------------------------------------------------------------
    // Pantallas
    // ------------------------------------------------------------------

    public function test_todas_las_pantallas_responden_para_un_administrador(): void
    {
        $admin = $this->usuario('admin');
        $categoria = $this->categoria();
        $producto = $this->producto(['precio_oferta' => 80, 'destacado' => true]);
        $this->producto(['stock' => 0, 'visible' => false]);
        $producto->moverStock(5, 'entrada', 'Compra', $admin->id);
        $pedido = $this->pedido($producto);
        $cancelado = $this->pedido($producto, 1, 'cancelado');
        $entregado = $this->pedido($producto, 1, 'entregado');
        $mensaje = Mensaje::create(['nombre' => 'Luis', 'correo' => 'luis@example.com', 'mensaje' => 'Hola, ¿tienen stock del Flip 6?']);

        $this->actingAs($admin);
        Bitacora::registrar('estado', 'pedidos', "Pasó el pedido {$pedido->codigo} de Pendiente a Confirmado");

        $pantallas = [
            '/panel',
            '/panel/productos',
            '/panel/productos?q=jbl&categoria='.$categoria->id.'&estado=publicados&stock=disponible&orden=precio-mayor',
            '/panel/productos?stock=bajo&orden=recientes',
            '/panel/productos?q=no-existe',
            '/panel/productos/create',
            "/panel/productos/{$producto->id}/edit",
            '/panel/productos/exportar?stock=agotado',
            '/panel/categorias',
            '/panel/categorias?q=nada&estado=ocultas',
            '/panel/categorias/create',
            "/panel/categorias/{$categoria->id}/edit",
            '/panel/inventario',
            '/panel/inventario?q=jbl&categoria='.$categoria->id.'&nivel=bajo',
            '/panel/inventario?nivel=agotado',
            '/panel/inventario/movimientos',
            '/panel/inventario/movimientos?tipo=venta&producto='.$producto->id.'&desde=2020-01-01&hasta=2099-12-31',
            '/panel/inventario/movimientos?desde=no-es-fecha',
            '/panel/inventario/movimientos/exportar',
            "/panel/inventario/{$producto->id}/ajustar",
            '/panel/pedidos',
            '/panel/pedidos?estado=pendiente&q=ana&desde=2020-01-01&hasta=2099-12-31',
            '/panel/pedidos?estado=enviado',
            '/panel/pedidos?q=no-existe',
            '/panel/pedidos/exportar?estado=cancelado',
            "/panel/pedidos/{$pedido->id}",
            "/panel/pedidos/{$cancelado->id}",
            "/panel/pedidos/{$entregado->id}",
            '/panel/mensajes',
            '/panel/mensajes?estado=no-leidos&q=stock',
            '/panel/mensajes?estado=leidos',
            "/panel/mensajes/{$mensaje->id}",
            '/panel/usuarios',
            '/panel/usuarios?q=x&rol=editor&estado=inactivos',
            '/panel/usuarios/create',
            "/panel/usuarios/{$admin->id}/edit",
            '/panel/bitacora',
            '/panel/bitacora?q=pedido&usuario='.urlencode($admin->name).'&modulo=pedidos&accion=estado&desde=2020-01-01&hasta=2099-12-31',
            '/panel/bitacora?q=no-existe',
            '/panel/perfil',
        ];

        foreach ($pantallas as $pantalla) {
            $respuesta = $this->get($pantalla);
            $this->assertSame(200, $respuesta->getStatusCode(), "La pantalla {$pantalla} no respondió 200.");
        }
    }

    public function test_las_pantallas_vacias_tambien_responden(): void
    {
        $this->actingAs($this->usuario('admin'));

        foreach (['/panel', '/panel/productos', '/panel/categorias', '/panel/inventario', '/panel/inventario/movimientos', '/panel/pedidos', '/panel/mensajes', '/panel/bitacora'] as $pantalla) {
            $this->get($pantalla)->assertOk();
        }
    }

    public function test_un_lector_ve_las_pantallas_sin_botones_para_modificar(): void
    {
        $producto = $this->producto();
        $pedido = $this->pedido($producto);
        $mensaje = Mensaje::create(['nombre' => 'Luis', 'correo' => 'luis@example.com', 'mensaje' => 'Hola, quiero información.']);
        $this->actingAs($this->usuario('lector'));

        $this->get('/panel/productos')->assertOk()->assertDontSee('Nuevo producto')->assertDontSee('Sí, eliminar');
        $this->get('/panel/categorias')->assertOk()->assertDontSee('Nueva categoría');
        $this->get('/panel/inventario')->assertOk()->assertDontSee('Ajustar el stock de');
        $this->get("/panel/pedidos/{$pedido->id}")->assertOk()->assertDontSee('Confirmar pedido');
        $this->get('/panel/mensajes')->assertOk()->assertDontSee('Sí, eliminar');
        $this->get("/panel/mensajes/{$mensaje->id}")->assertOk()->assertDontSee('Eliminar mensaje');
        $this->get('/panel/perfil')->assertOk();
    }

    public function test_las_exportaciones_son_csv_con_bom_y_punto_y_coma(): void
    {
        $producto = $this->producto(['nombre' => 'JBL Charge 5']);
        $this->pedido($producto);
        $this->actingAs($this->usuario('admin'));

        $productos = $this->get('/panel/productos/exportar')->assertOk()->streamedContent();
        $this->assertStringStartsWith("\xEF\xBB\xBFSKU;Nombre;", $productos);
        $this->assertStringContainsString('JBL Charge 5', $productos);

        $pedidos = $this->get('/panel/pedidos/exportar')->assertOk()->streamedContent();
        $this->assertStringStartsWith("\xEF\xBB\xBF", $pedidos);
        $this->assertStringContainsString('JBL-000001;', $pedidos);

        $movimientos = $this->get('/panel/inventario/movimientos/exportar')->assertOk()->streamedContent();
        $this->assertStringContainsString('Venta;-2;8;', $movimientos);
    }

    // ------------------------------------------------------------------
    // Roles
    // ------------------------------------------------------------------

    public function test_un_lector_no_puede_modificar_nada(): void
    {
        $producto = $this->producto();
        $categoria = Categoria::first();
        $pedido = $this->pedido($producto);
        $mensaje = Mensaje::create(['nombre' => 'Luis', 'correo' => 'luis@example.com', 'mensaje' => 'Hola, quiero información.']);
        $stock = $producto->fresh()->stock;

        $this->actingAs($this->usuario('lector'));

        $intentos = [
            ['post', '/panel/productos', $this->datosProducto()],
            ['put', "/panel/productos/{$producto->id}", $this->datosProducto(['nombre' => 'Cambiado'])],
            ['patch', "/panel/productos/{$producto->id}/visible", []],
            ['delete', "/panel/productos/{$producto->id}", []],
            ['post', '/panel/categorias', ['nombre' => 'Nueva']],
            ['delete', "/panel/categorias/{$categoria->id}", []],
            ['post', "/panel/inventario/{$producto->id}/ajustar", ['tipo' => 'entrada', 'cantidad' => 5, 'motivo' => 'Compra']],
            ['patch', "/panel/pedidos/{$pedido->id}/estado", ['estado' => 'confirmado']],
            ['patch', "/panel/mensajes/{$mensaje->id}/leido", []],
            ['delete', "/panel/mensajes/{$mensaje->id}", []],
        ];

        foreach ($intentos as [$metodo, $ruta, $datos]) {
            $this->{$metodo}($ruta, $datos)
                ->assertRedirect()
                ->assertSessionHas('error', 'Tu rol es de solo lectura: no puedes hacer cambios.');
        }

        // Nada cambió.
        $this->assertSame(1, Producto::count());
        $this->assertSame(1, Categoria::count());
        $this->assertSame($stock, $producto->fresh()->stock);
        $this->assertTrue($producto->fresh()->visible);
        $this->assertSame('JBL Prueba 1', $producto->fresh()->nombre);
        $this->assertSame('pendiente', $pedido->fresh()->estado);
        $this->assertFalse($mensaje->fresh()->leido);
        $this->assertSame(1, MovimientoInventario::count());
    }

    public function test_un_lector_si_puede_editar_su_perfil(): void
    {
        $lector = $this->usuario('lector');

        $this->actingAs($lector)
            ->put('/panel/perfil', ['name' => 'Nombre Nuevo', 'email' => $lector->email])
            ->assertRedirect(route('perfil.editar'))
            ->assertSessionHas('ok');

        $this->assertSame('Nombre Nuevo', $lector->fresh()->name);
    }

    public function test_un_editor_no_entra_a_usuarios_ni_a_la_bitacora(): void
    {
        $editor = $this->usuario('editor');
        $otro = $this->usuario('admin');
        $this->actingAs($editor);

        foreach (['/panel/usuarios', '/panel/usuarios/create', "/panel/usuarios/{$otro->id}/edit", '/panel/bitacora'] as $ruta) {
            $this->get($ruta)
                ->assertRedirect(route('panel'))
                ->assertSessionHas('error', 'Tu rol no tiene acceso a ese módulo.');
        }

        $this->delete("/panel/usuarios/{$otro->id}")->assertRedirect(route('panel'));
        $this->assertNotNull($otro->fresh());

        // Pero sí trabaja con el catálogo.
        $this->get('/panel/productos/create')->assertOk();
    }

    // ------------------------------------------------------------------
    // Productos y categorías
    // ------------------------------------------------------------------

    public function test_crear_un_producto_guarda_la_foto_y_registra_el_stock_inicial(): void
    {
        $this->fotosAntes = $this->fotosSubidas();
        $admin = $this->usuario('admin');

        $this->actingAs($admin)
            ->post('/panel/productos', $this->datosProducto([
                'foto' => UploadedFile::fake()->image('flip-6.jpg', 800, 600),
            ]))
            ->assertRedirect(route('productos.index'))
            ->assertSessionHas('ok');

        $producto = Producto::firstOrFail();
        $this->assertSame('jbl-flip-6', $producto->slug);
        $this->assertSame(7, $producto->stock);
        $this->assertSame(2, $producto->stock_minimo);
        $this->assertTrue($producto->enOferta());
        $this->assertStringStartsWith('uploads/productos/jbl-flip-6-', $producto->foto);
        $this->assertFileExists(public_path($producto->foto));

        // El stock no se escribió directo: entró como un movimiento.
        $this->assertDatabaseHas('movimientos_inventario', [
            'producto_id' => $producto->id,
            'user_id' => $admin->id,
            'tipo' => 'entrada',
            'cantidad' => 7,
            'stock_resultante' => 7,
            'motivo' => 'Stock inicial',
        ]);
        $this->assertDatabaseHas('bitacora', ['accion' => 'crear', 'modulo' => 'productos', 'user_id' => $admin->id]);
    }

    public function test_el_formulario_de_producto_valida_la_foto_y_el_precio_de_oferta(): void
    {
        $this->actingAs($this->usuario('admin'))
            ->post('/panel/productos', $this->datosProducto(['precio' => '100', 'precio_oferta' => '120']))
            ->assertSessionHasErrors(['foto', 'precio_oferta']);

        $this->assertSame(0, Producto::count());
    }

    public function test_editar_un_producto_no_cambia_su_stock(): void
    {
        $producto = $this->producto(['stock' => 10]);
        $slug = $producto->slug;

        $this->actingAs($this->usuario('editor'))
            ->put("/panel/productos/{$producto->id}", $this->datosProducto(['nombre' => 'JBL Go 4', 'stock' => 999, 'stock_inicial' => 999]))
            ->assertRedirect(route('productos.index'));

        $producto->refresh();
        $this->assertSame('JBL Go 4', $producto->nombre);
        // El slug no cambia al editar: los enlaces de la tienda lo usan.
        $this->assertSame($slug, $producto->slug);
        $this->assertSame(10, $producto->stock);
    }

    public function test_publicar_y_ocultar_un_producto(): void
    {
        $producto = $this->producto();

        $this->actingAs($this->usuario('editor'))
            ->patch("/panel/productos/{$producto->id}/visible")
            ->assertSessionHas('ok');

        $this->assertFalse($producto->fresh()->visible);
    }

    public function test_un_producto_con_pedidos_no_se_elimina(): void
    {
        $vendido = $this->producto();
        $sinVentas = $this->producto();
        $this->pedido($vendido);
        $this->actingAs($this->usuario('admin'));

        $this->delete("/panel/productos/{$vendido->id}")->assertSessionHas('error');
        $this->assertNotNull($vendido->fresh());

        $this->delete("/panel/productos/{$sinVentas->id}")->assertSessionHas('ok');
        $this->assertNull($sinVentas->fresh());
    }

    public function test_crear_y_eliminar_categorias(): void
    {
        $this->actingAs($this->usuario('editor'));

        $this->post('/panel/categorias', ['nombre' => 'Audio para casa', 'visible' => '1'])
            ->assertRedirect(route('categorias.index'));

        $categoria = Categoria::firstOrFail();
        $this->assertSame('audio-para-casa', $categoria->slug);

        // Con productos no se puede eliminar.
        $producto = $this->producto(['categoria_id' => $categoria->id]);
        $this->delete("/panel/categorias/{$categoria->id}")->assertSessionHas('error');
        $this->assertNotNull($categoria->fresh());

        $producto->delete();
        $this->delete("/panel/categorias/{$categoria->id}")->assertSessionHas('ok');
        $this->assertNull($categoria->fresh());
    }

    // ------------------------------------------------------------------
    // Inventario
    // ------------------------------------------------------------------

    public function test_una_entrada_suma_al_stock(): void
    {
        $producto = $this->producto(['stock' => 10]);
        $editor = $this->usuario('editor');

        $this->actingAs($editor)
            ->post("/panel/inventario/{$producto->id}/ajustar", ['tipo' => 'entrada', 'cantidad' => 5, 'motivo' => 'Compra al proveedor'])
            ->assertRedirect(route('inventario.index'))
            ->assertSessionHas('ok');

        $this->assertSame(15, $producto->fresh()->stock);
        $this->assertDatabaseHas('movimientos_inventario', [
            'producto_id' => $producto->id, 'user_id' => $editor->id, 'tipo' => 'entrada',
            'cantidad' => 5, 'stock_resultante' => 15, 'motivo' => 'Compra al proveedor',
        ]);
        $this->assertDatabaseHas('bitacora', ['accion' => 'stock', 'modulo' => 'inventario']);
    }

    public function test_una_salida_no_puede_dejar_el_stock_en_negativo(): void
    {
        $producto = $this->producto(['stock' => 4]);
        $this->actingAs($this->usuario('editor'));

        $this->post("/panel/inventario/{$producto->id}/ajustar", ['tipo' => 'salida', 'cantidad' => 5, 'motivo' => 'Merma'])
            ->assertSessionHasErrors('cantidad');

        $this->assertSame(4, $producto->fresh()->stock);
        $this->assertSame(0, MovimientoInventario::count());

        // Sacar exactamente lo que hay sí se puede.
        $this->post("/panel/inventario/{$producto->id}/ajustar", ['tipo' => 'salida', 'cantidad' => 4, 'motivo' => 'Merma'])
            ->assertSessionHasNoErrors();

        $this->assertSame(0, $producto->fresh()->stock);
        $this->assertDatabaseHas('movimientos_inventario', ['tipo' => 'salida', 'cantidad' => -4, 'stock_resultante' => 0]);
    }

    public function test_un_ajuste_fija_el_stock_exacto_y_registra_la_diferencia(): void
    {
        $producto = $this->producto(['stock' => 10]);
        $this->actingAs($this->usuario('editor'));

        $this->post("/panel/inventario/{$producto->id}/ajustar", ['tipo' => 'ajuste', 'cantidad' => 3, 'motivo' => 'Conteo de fin de mes'])
            ->assertSessionHasNoErrors();

        $this->assertSame(3, $producto->fresh()->stock);
        $this->assertDatabaseHas('movimientos_inventario', ['tipo' => 'ajuste', 'cantidad' => -7, 'stock_resultante' => 3]);

        // Ajustar al mismo número no registra nada.
        $this->post("/panel/inventario/{$producto->id}/ajustar", ['tipo' => 'ajuste', 'cantidad' => 3, 'motivo' => 'Conteo'])
            ->assertSessionHasErrors('cantidad');
        $this->assertSame(1, MovimientoInventario::count());
    }

    public function test_el_ajuste_exige_motivo_y_un_tipo_manual(): void
    {
        $producto = $this->producto(['stock' => 10]);
        $this->actingAs($this->usuario('editor'));

        $this->post("/panel/inventario/{$producto->id}/ajustar", ['tipo' => 'venta', 'cantidad' => 0, 'motivo' => ''])
            ->assertSessionHasErrors(['tipo', 'cantidad', 'motivo']);

        $this->assertSame(10, $producto->fresh()->stock);
    }

    // ------------------------------------------------------------------
    // Pedidos
    // ------------------------------------------------------------------

    public function test_un_pedido_avanza_por_sus_estados(): void
    {
        $pedido = $this->pedido($this->producto());
        $this->actingAs($this->usuario('editor'));

        foreach (['confirmado', 'enviado', 'entregado'] as $estado) {
            $this->patch("/panel/pedidos/{$pedido->id}/estado", ['estado' => $estado])
                ->assertRedirect(route('pedidos.show', $pedido))
                ->assertSessionHas('ok');

            $this->assertSame($estado, $pedido->fresh()->estado);
        }

        $this->assertSame(3, Bitacora::where('modulo', 'pedidos')->where('accion', 'estado')->count());
    }

    public function test_cancelar_un_pedido_devuelve_el_stock(): void
    {
        $producto = $this->producto(['stock' => 10]);
        $pedido = $this->pedido($producto, 2);
        $editor = $this->usuario('editor');
        $this->assertSame(8, $producto->fresh()->stock);

        $this->actingAs($editor)
            ->patch("/panel/pedidos/{$pedido->id}/estado", ['estado' => 'cancelado'])
            ->assertRedirect(route('pedidos.show', $pedido))
            ->assertSessionHas('ok');

        $this->assertSame('cancelado', $pedido->fresh()->estado);
        $this->assertSame(10, $producto->fresh()->stock);
        $this->assertDatabaseHas('movimientos_inventario', [
            'producto_id' => $producto->id, 'pedido_id' => $pedido->id, 'user_id' => $editor->id,
            'tipo' => 'devolucion', 'cantidad' => 2, 'stock_resultante' => 10,
            'motivo' => "Cancelación del pedido {$pedido->codigo}",
        ]);

        // Un pedido cancelado ya no cambia: el stock no se devuelve dos veces.
        $this->patch("/panel/pedidos/{$pedido->id}/estado", ['estado' => 'cancelado'])->assertSessionHas('error');
        $this->assertSame(10, $producto->fresh()->stock);
    }

    public function test_cancelar_un_pedido_cuyo_producto_ya_no_existe_no_falla(): void
    {
        $producto = $this->producto();
        $pedido = $this->pedido($producto);
        $producto->delete();

        $this->actingAs($this->usuario('editor'))
            ->patch("/panel/pedidos/{$pedido->id}/estado", ['estado' => 'cancelado'])
            ->assertSessionHas('ok');

        $this->assertSame('cancelado', $pedido->fresh()->estado);
        $this->assertSame(0, MovimientoInventario::where('tipo', 'devolucion')->count());
    }

    public function test_una_transicion_invalida_de_estado_se_rechaza(): void
    {
        $pedido = $this->pedido($this->producto());
        $this->actingAs($this->usuario('editor'));

        // De pendiente no se salta a entregado.
        $this->patch("/panel/pedidos/{$pedido->id}/estado", ['estado' => 'entregado'])->assertSessionHas('error');
        $this->assertSame('pendiente', $pedido->fresh()->estado);

        // Un estado que no existe ni siquiera pasa la validación.
        $this->patch("/panel/pedidos/{$pedido->id}/estado", ['estado' => 'regalado'])->assertSessionHasErrors('estado');
        $this->assertSame('pendiente', $pedido->fresh()->estado);
    }

    // ------------------------------------------------------------------
    // Mensajes
    // ------------------------------------------------------------------

    public function test_abrir_un_mensaje_lo_marca_leido_y_eliminarlo_queda_en_la_bitacora(): void
    {
        $mensaje = Mensaje::create(['nombre' => 'Luis', 'correo' => 'luis@example.com', 'mensaje' => 'Hola, quiero información.']);
        $this->actingAs($this->usuario('editor'));

        $this->get("/panel/mensajes/{$mensaje->id}")->assertOk()->assertSee('Responder por correo');
        $this->assertTrue($mensaje->fresh()->leido);

        $this->delete("/panel/mensajes/{$mensaje->id}")->assertRedirect(route('mensajes.index'));
        $this->assertNull($mensaje->fresh());
        $this->assertDatabaseHas('bitacora', ['accion' => 'eliminar', 'modulo' => 'mensajes']);
    }

    // ------------------------------------------------------------------
    // Usuarios y perfil
    // ------------------------------------------------------------------

    public function test_un_administrador_crea_usuarios_con_rol(): void
    {
        $this->actingAs($this->usuario('admin'))
            ->post('/panel/usuarios', [
                'name' => 'Eva Ríos',
                'email' => 'eva@jbl.pe',
                'password' => 'secreta123',
                'password_confirmation' => 'secreta123',
                'rol' => 'lector',
                'activo' => '1',
            ])
            ->assertRedirect(route('usuarios.index'));

        $eva = User::where('email', 'eva@jbl.pe')->firstOrFail();
        $this->assertSame('lector', $eva->rol);
        $this->assertTrue($eva->activo);
        $this->assertTrue(password_verify('secreta123', $eva->password));
    }

    public function test_no_se_puede_eliminar_al_ultimo_administrador(): void
    {
        $admin = $this->usuario('admin');
        $this->usuario('editor');

        $this->actingAs($admin)
            ->delete("/panel/usuarios/{$admin->id}")
            ->assertSessionHas('error');

        $this->assertNotNull($admin->fresh());
        $this->assertSame(1, User::where('rol', 'admin')->where('activo', true)->count());
    }

    public function test_nadie_se_desactiva_ni_se_quita_el_rol_de_administrador(): void
    {
        $admin = $this->usuario('admin');
        $this->usuario('admin');   // aunque exista otro administrador
        $this->actingAs($admin);

        $datos = ['name' => $admin->name, 'email' => $admin->email];

        $this->put("/panel/usuarios/{$admin->id}", $datos + ['rol' => 'editor', 'activo' => '1'])
            ->assertSessionHasErrors('rol');

        $this->put("/panel/usuarios/{$admin->id}", $datos + ['rol' => 'admin', 'activo' => '0'])
            ->assertSessionHasErrors('activo');

        $this->assertSame('admin', $admin->fresh()->rol);
        $this->assertTrue($admin->fresh()->activo);
    }

    public function test_un_administrador_cambia_el_rol_y_el_acceso_de_otro_usuario(): void
    {
        $admin = $this->usuario('admin');
        $otro = $this->usuario('admin');

        $this->actingAs($admin)
            ->put("/panel/usuarios/{$otro->id}", ['name' => $otro->name, 'email' => $otro->email, 'rol' => 'lector', 'activo' => '0'])
            ->assertRedirect(route('usuarios.index'));

        $this->assertSame('lector', $otro->fresh()->rol);
        $this->assertFalse($otro->fresh()->activo);

        $this->delete("/panel/usuarios/{$otro->id}")->assertSessionHas('ok');
        $this->assertNull($otro->fresh());
    }

    public function test_cambiar_la_contrasena_del_perfil_exige_la_actual(): void
    {
        $usuario = $this->usuario('editor', ['password' => 'clave-vieja-1']);
        $this->actingAs($usuario);

        $datos = ['name' => $usuario->name, 'email' => $usuario->email, 'password' => 'clave-nueva-1', 'password_confirmation' => 'clave-nueva-1'];

        $this->put('/panel/perfil', $datos)->assertSessionHasErrors('password_actual');
        $this->put('/panel/perfil', $datos + ['password_actual' => 'no-es-la-clave'])->assertSessionHasErrors('password_actual');
        $this->assertTrue(password_verify('clave-vieja-1', $usuario->fresh()->password));

        $this->put('/panel/perfil', $datos + ['password_actual' => 'clave-vieja-1'])->assertSessionHasNoErrors();
        $this->assertTrue(password_verify('clave-nueva-1', $usuario->fresh()->password));
    }

    // ------------------------------------------------------------------
    // Ingreso
    // ------------------------------------------------------------------

    public function test_una_cuenta_inactiva_no_puede_ingresar(): void
    {
        $this->usuario('editor', ['email' => 'inactivo@jbl.pe', 'password' => 'secreta123', 'activo' => false]);

        $this->post('/login', ['email' => 'inactivo@jbl.pe', 'password' => 'secreta123'])
            ->assertSessionHasErrors(['email' => 'Tu cuenta está desactivada. Pide acceso a un administrador.']);

        $this->assertGuest();
        $this->assertSame(0, Bitacora::count());
    }

    public function test_ingresar_y_salir_quedan_en_la_bitacora(): void
    {
        $usuario = $this->usuario('editor', ['email' => 'eva@jbl.pe', 'password' => 'secreta123']);

        $this->post('/login', ['email' => 'eva@jbl.pe', 'password' => 'secreta123'])->assertRedirect(route('panel'));

        $this->assertAuthenticatedAs($usuario);
        $this->assertNotNull($usuario->fresh()->ultimo_ingreso);
        $this->assertDatabaseHas('bitacora', ['accion' => 'ingresar', 'user_id' => $usuario->id]);

        $this->post('/panel/salir')->assertRedirect(route('login'));

        $this->assertGuest();
        $this->assertDatabaseHas('bitacora', ['accion' => 'salir', 'user_id' => $usuario->id]);
    }

    public function test_una_contrasena_incorrecta_no_ingresa(): void
    {
        $this->usuario('admin', ['email' => 'eva@jbl.pe', 'password' => 'secreta123']);

        $this->post('/login', ['email' => 'eva@jbl.pe', 'password' => 'otra-clave'])->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
