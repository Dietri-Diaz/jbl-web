# JBL ERP — Panel de la tienda

Sistema de administración de la tienda de demostración de JBL, hecho con
**Laravel 12** y vistas Blade. Desde aquí se administran los productos y sus
categorías, se controla el inventario, se atienden los pedidos que llegan de la
tienda, se leen los mensajes del formulario de contacto y se gestionan los
usuarios del panel. También expone la API que usa la tienda.

Es un proyecto académico: los precios, el stock, los clientes y los pedidos son
datos de ejemplo. No se cobra ni se envía nada.

## Módulos

| Módulo | Qué permite |
|--------|-------------|
| **Inicio** | Indicadores: ventas del mes y de hoy, pedidos pendientes, productos publicados, stock bajo y mensajes sin leer. Gráficos de ventas de los últimos 14 días, pedidos por estado y productos por categoría. Listas de últimos pedidos, productos por reponer, más vendidos y actividad reciente |
| **Pedidos** | Listado con pestañas por estado, búsqueda por código o cliente y rango de fechas; detalle con su línea de tiempo; cambio de estado y exportación a CSV |
| **Mensajes** | Leer los mensajes del formulario de contacto (al abrir uno queda como leído), marcarlos como leídos o no leídos y eliminarlos |
| **Productos** | Agregar, editar y eliminar productos con su foto, categoría, código (SKU), precio, precio de oferta, ficha técnica y sello; publicarlos u ocultarlos; destacarlos; exportar a CSV |
| **Categorías** | Crear, editar, ordenar y eliminar las categorías del catálogo |
| **Existencias** | Stock de cada producto, valor del inventario, productos con stock bajo y agotados. Desde aquí se registra una entrada, una salida o un ajuste, siempre con su motivo |
| **Movimientos** | Historial de cada cambio de stock (entrada, salida, ajuste, venta, devolución), con filtros y exportación a CSV |
| **Usuarios** | Crear cuentas, asignar el rol y activar o desactivar el acceso. Solo administrador |
| **Bitácora** | Registro de ingresos, salidas, creaciones, ediciones, eliminaciones, cambios de estado y movimientos de stock, con usuario, módulo y fecha. Solo administrador |
| **Mi perfil** | Cada usuario cambia su nombre, su correo y su contraseña (pide la contraseña actual) |

Reglas que el sistema hace cumplir:

- **El stock solo cambia con un movimiento.** Al crear un producto se escribe el
  stock inicial; después solo se modifica desde Existencias, con una venta o con
  una devolución. Editar un producto no toca su stock.
- **Un pedido avanza en orden:** Pendiente → Confirmado → Enviado → Entregado.
  Se puede cancelar mientras no esté entregado, y al cancelarlo sus unidades
  vuelven al inventario. Un pedido cancelado no cuenta como venta.
- Un producto que aparece en pedidos no se elimina (se puede ocultar), y una
  categoría con productos tampoco.
- Nadie puede eliminar ni desactivar su propia cuenta, ni quitarse el rol de
  administrador, y siempre queda al menos un administrador activo.

## Roles

Los permisos están en `app/Http/Middleware/VerificarRol.php`.

| Rol | Qué puede hacer |
|-----|-----------------|
| Administrador (`admin`) | Todo, incluidos los módulos Usuarios y Bitácora |
| Editor (`editor`) | Ver y modificar todos los módulos, menos Usuarios y Bitácora |
| Solo lectura (`lector`) | Ver los mismos módulos que el editor y descargar los CSV. No puede crear, editar ni eliminar nada; solo cerrar sesión y editar su propio perfil |

Una cuenta desactivada no puede ingresar, y si tenía la sesión abierta la
pierde en su siguiente clic.

## Requisitos

- PHP 8.2 o superior (el de XAMPP sirve)
- Composer
- MySQL o MariaDB (el de XAMPP sirve)

## Instalación

Desde la carpeta `erp/`:

```bash
composer install
cp .env.example .env          # en la consola de Windows: copy .env.example .env
php artisan key:generate
```

Crea la base de datos `jbl_erp` (por ejemplo, en phpMyAdmin) y carga las tablas
con los datos de ejemplo:

```bash
php artisan migrate --seed
```

Los datos de ejemplo son:

| Qué | Cuántos | Detalle |
|-----|---------|---------|
| Usuarios | 3 | Uno por rol |
| Categorías | 5 | Parlantes portátiles, Parlantes de fiesta, Audífonos, Audio para casa y Audio profesional |
| Productos | 26 | Modelos y fotos reales; precios y stock de ejemplo |
| Pedidos | 22 | De las últimas dos semanas, en todos los estados, con su detalle y sus movimientos de inventario |
| Mensajes | 5 | Consultas de ejemplo |
| Bitácora | 26 registros | Actividad de ejemplo |

Los nombres y correos de los clientes son inventados (dominio `example.com`).

## Uso

```bash
php artisan serve
```

Abre <http://127.0.0.1:8000> e ingresa con una de estas cuentas:

| Correo | Rol | Contraseña |
|--------|-----|------------|
| `admin@jbl.pe` | Administrador | `admin123` |
| `editor@jbl.pe` | Editor | `admin123` |
| `consulta@jbl.pe` | Solo lectura | `admin123` |

Son contraseñas de demostración: hay que cambiarlas (en **Mi perfil** o en
**Usuarios**) antes de usar el sistema fuera de una demostración.

## Cómo se conecta con la tienda

La tienda no necesita el ERP para funcionar: si el ERP está apagado, usa la
copia estática `data/catalogo.js`. Cuando está encendido:

- `js/tienda.js` pide `GET /api/catalogo` y, si responde en menos de 2,5
  segundos, usa esos datos: precios y stock al día.
- `js/checkout.js` envía el carrito a `POST /api/pedidos`. El ERP vuelve a
  validar todo, calcula los precios con los de la base de datos, descuenta el
  stock y responde con el código del pedido.
- `js/pedido.js` consulta `GET /api/pedidos/{codigo}?correo=` para el
  seguimiento.
- `js/contacto.js` envía el formulario a `POST /api/mensajes`.

Para probarlo, sirve la tienda en el puerto 5500 desde la raíz del repositorio:

```bash
python -m http.server 5500
```

La dirección del ERP que usa la tienda se puede cambiar con el atributo
`data-erp` de la etiqueta `<html>`, por ejemplo
`<html lang="es" data-erp="https://erp.ejemplo.com">`.

### Copia estática del catálogo

```bash
php artisan tienda:exportar
```

Escribe `data/catalogo.js` (en la raíz del repositorio) con las reglas, las
categorías y los productos publicados, y copia a `assets/img/productos/` las
fotos subidas desde el panel. Conviene ejecutarlo después de cambiar productos
o precios y antes de publicar la tienda. Ese archivo no se edita a mano.

## API

| Método y ruta | Uso | Respuestas |
|---------------|-----|------------|
| `GET /api/catalogo` | Todo lo que la tienda necesita en una sola petición | `200` con `{ "tienda": {...}, "categorias": [...], "productos": [...] }`. Solo incluye productos publicados de categorías visibles |
| `POST /api/pedidos` | Registra un pedido: `nombre`, `correo`, `telefono`, `documento` (opcional), `entrega`, `direccion`, `distrito`, `referencia` (opcional), `pago`, `notas` (opcional) e `items` (`id` y `cantidad`) | `201` con el resumen del pedido y su código · `422` datos inválidos, producto no disponible o stock insuficiente · `429` más de 10 pedidos por minuto |
| `GET /api/pedidos/{codigo}?correo=` | Seguimiento de un pedido | `200` con el resumen y el estado actual · `404` si el código y el correo no coinciden · `429` más de 30 consultas por minuto |
| `POST /api/mensajes` | Guarda un mensaje del formulario (`nombre`, `correo`, `mensaje`) | `201` guardado · `422` datos inválidos · `429` más de 5 envíos por minuto |

Detalles:

- El resumen de un pedido trae `codigo`, `estado`, `estado_nombre`, `fecha`,
  `cliente`, `entrega`, `direccion`, `distrito`, `pago`, `subtotal`, `envio`,
  `total` e `items`.
- En un pedido, la dirección y el distrito son obligatorios solo si la entrega
  es a domicilio. El celular debe tener 9 dígitos y empezar con 9; el documento,
  si se escribe, es un DNI (8 dígitos) o un RUC (11 dígitos).
- El formulario de contacto tiene un campo trampa (`sitio_web`) invisible para
  las personas. Si llega lleno, lo llenó un bot: la API responde `201` pero no
  guarda el mensaje.
- Las reglas de la tienda (costo de envío, monto del envío gratis, máximo por
  producto, formas de entrega y de pago, estados y roles) están en
  `config/tienda.php`.

## Pruebas

```bash
php artisan test
```

Son 46 pruebas (333 aserciones) que corren sobre SQLite en memoria, así que no
tocan la base `jbl_erp`:

| Archivo | Pruebas | Qué cubre |
|---------|---------|-----------|
| `tests/Feature/ExampleTest.php` | 3 | La raíz redirige al panel, el panel exige sesión y el login carga |
| `tests/Feature/ApiLandingTest.php` | 12 | Catálogo, pedidos, seguimiento y mensajes de la API |
| `tests/Feature/PanelTest.php` | 30 | Pantallas, roles, productos, categorías, inventario, pedidos, mensajes, usuarios, perfil y acceso |
| `tests/Unit/ExampleTest.php` | 1 | Prueba de ejemplo que trae Laravel |

Necesitan la extensión GD de PHP, porque generan imágenes de prueba para la
foto de los productos.

## Estructura

```
erp/
├── app/Console/Commands/        Comando tienda:exportar
├── app/Http/Controllers/        Controladores del panel y de la API (Api/)
├── app/Http/Middleware/         VerificarRol: permisos según el rol
├── app/Http/Requests/           Validación del formulario de producto
├── app/Models/                  User, Categoria, Producto, Pedido, PedidoItem,
│                                MovimientoInventario, Mensaje y Bitacora
├── app/Soporte/                 CatalogoTienda (arma el catálogo) y Csv (exportaciones)
├── config/tienda.php            Reglas de la tienda
├── database/migrations/         Tablas de la base de datos
├── database/seeders/            Usuarios, catálogo y actividad de ejemplo
├── lang/es/validation.php       Mensajes de validación en español
├── public/css/erp.css           Estilos del panel
├── public/js/                   JavaScript del panel y Chart.js para los gráficos
├── resources/views/erp/         Vistas del panel
├── routes/erp/                  Rutas de cada módulo del panel
├── routes/api/                  Rutas de la API pública
└── tests/Feature/               Pruebas automáticas
```

Cada módulo tiene su propio archivo de rutas en `routes/erp/` o `routes/api/`.
Así cada integrante agrega el suyo sin tocar los archivos de los demás.
