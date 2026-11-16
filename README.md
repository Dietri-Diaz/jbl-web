# JBL Store — Tienda de demostración

Tienda en línea de la marca **JBL** (parlantes, audífonos y equipos de audio)
con su **ERP** hecho en Laravel. En la tienda se recorre el catálogo, se arma
un carrito, se registra un pedido y se le hace seguimiento. Desde el ERP se
administran los productos, las categorías, el inventario, los pedidos, los
mensajes y los usuarios.

> **Proyecto académico, sitio no oficial.** La tienda no cobra ni envía nada.
> Los modelos y las fotografías son reales; los precios, el stock, los clientes
> y los pedidos son datos inventados para la demostración.

## Cómo verlo

Abre `index.html` en el navegador. No hace falta servidor ni instalar nada: si
el ERP no está encendido, la tienda usa la copia del catálogo que viene en
`data/catalogo.js` y los pedidos se guardan solo en el navegador, como
demostración.

Para verla conectada (precios y stock al día, pedidos registrados en la base de
datos), enciende el ERP (instrucciones en [`erp/README.md`](erp/README.md)) y
sirve la tienda en el puerto 5500 desde la raíz del repositorio:

```
python -m http.server 5500
```

Tienda: <http://127.0.0.1:5500> · Panel del ERP: <http://127.0.0.1:8000>

## Tecnologías

| Capa | Tecnología |
|------|------------|
| Tienda | HTML5, CSS3 (variables nativas, Flexbox y Grid) y JavaScript sin framework |
| Carrito | `localStorage` del navegador |
| Datos de la tienda | API del ERP cuando está encendido; si no, la copia estática `data/catalogo.js` |
| ERP (backend y panel) | Laravel 12 sobre PHP 8.2, vistas Blade |
| Base de datos | MySQL / MariaDB (base `jbl_erp`) |
| Pruebas | PHPUnit sobre SQLite en memoria y un script de Node.js para la tienda |
| CI/CD | Jenkins en Docker, GitHub Actions y Vercel |
| Control de versiones | Git y GitHub |

## Páginas de la tienda

| Página | Qué hace |
|--------|----------|
| `index.html` | Inicio: portada, cifras de la marca, categorías, destacados, ofertas, bloques de marca y formulario de contacto |
| `catalogo.html` | Catálogo con búsqueda, filtros (categoría, precio, oferta, stock, resistencia al agua) y orden. Los filtros viajan en la dirección, así se pueden compartir |
| `producto.html` | Ficha de un producto (`producto.html?p=flip-5`): foto, precio, stock, ficha técnica y productos relacionados |
| `carrito.html` | Carrito: cambiar cantidades, quitar productos y ver el resumen con el envío |
| `checkout.html` | Finalizar compra: datos del cliente, entrega (envío a domicilio o recojo en tienda) y forma de pago |
| `pedido.html` | Confirmación del pedido y seguimiento con el código y el correo |

Reglas de la tienda (están en `erp/config/tienda.php`): envío a S/ 12, gratis
desde S/ 299 o con recojo en tienda, y un máximo de 5 unidades de un mismo
producto por pedido.

### De dónde salen los datos

`js/tienda.js` decide al cargar cada página:

| Situación | Qué usa la tienda | Qué pasa con el pedido |
|-----------|-------------------|------------------------|
| El ERP responde a `GET /api/catalogo` en menos de 2,5 segundos | Los datos del ERP: precios y stock al día | Se registra en la base de datos con un código `JBL-000001` y descuenta el stock |
| El ERP no responde o no hay ERP configurado | La copia estática `data/catalogo.js` | Es un pedido de demostración: se guarda solo en el navegador, con un código `DEMO-xxxxxx` |

La tienda busca el ERP en `http://127.0.0.1:8000` únicamente cuando se abre en
`localhost`, en `127.0.0.1` o como archivo. En cualquier otro dominio solo lo
busca si la etiqueta `<html>` trae el atributo `data-erp="https://..."`. Por eso
el sitio publicado en Vercel funciona con la copia estática.

`data/catalogo.js` **no se edita a mano**: se genera desde el ERP con
`php artisan tienda:exportar`.

## ERP

| Módulo | Qué permite |
|--------|-------------|
| Inicio | Indicadores (ventas del mes y de hoy, pedidos pendientes, stock bajo, mensajes sin leer) y gráficos de ventas, estados y categorías |
| Pedidos | Ver los pedidos de la tienda, filtrarlos, cambiar su estado y exportarlos a CSV. Cancelar un pedido devuelve su stock |
| Mensajes | Leer, marcar y eliminar los mensajes del formulario de contacto |
| Productos | Agregar, editar, publicar u ocultar productos con su foto, precio, oferta y ficha técnica; exportar a CSV |
| Categorías | Agrupar los productos |
| Existencias | Ver el stock de cada producto y registrar entradas, salidas y ajustes |
| Movimientos | Historial de cada cambio de stock, con exportación a CSV |
| Usuarios | Crear cuentas, asignar roles y activar o desactivar el acceso (solo administrador) |
| Bitácora | Registro de quién hizo qué y cuándo (solo administrador) |
| Mi perfil | Cada usuario cambia su nombre, su correo y su contraseña |

### Roles y cuentas de demostración

| Rol | Qué puede hacer | Cuenta | Contraseña |
|-----|-----------------|--------|------------|
| Administrador (`admin`) | Todo, incluidos Usuarios y Bitácora | `admin@jbl.pe` | `admin123` |
| Editor (`editor`) | Ver y modificar todo, menos Usuarios y Bitácora | `editor@jbl.pe` | `admin123` |
| Solo lectura (`lector`) | Ver y exportar; no puede modificar nada, salvo su propio perfil | `consulta@jbl.pe` | `admin123` |

### API pública

| Método y ruta | Uso |
|---------------|-----|
| `GET /api/catalogo` | Reglas de la tienda, categorías y productos publicados |
| `POST /api/pedidos` | Registra un pedido (máximo 10 por minuto) |
| `GET /api/pedidos/{codigo}?correo=` | Seguimiento de un pedido; solo responde si el correo coincide |
| `POST /api/mensajes` | Guarda un mensaje del formulario de contacto (máximo 5 por minuto) |

El detalle de cada respuesta está en [`erp/README.md`](erp/README.md).

## Comandos

Dentro de `erp/`:

```bash
php artisan migrate --seed     # crea las tablas y carga los datos de ejemplo
php artisan serve              # enciende el ERP en http://127.0.0.1:8000
php artisan tienda:exportar    # vuelve a generar data/catalogo.js
php artisan test               # 46 pruebas del ERP, sobre SQLite en memoria
```

Desde la raíz:

```bash
node scripts/verificar-landing.mjs   # revisa las 6 páginas, el catálogo, las fotos y la sintaxis de js/
mysql -u root -p < database/schema.sql   # carga la base completa sin usar Laravel
```

Los datos de ejemplo son 3 usuarios (uno por rol), 5 categorías, 26 productos,
22 pedidos, 5 mensajes y la bitácora.

## Decisiones de diseño

| Decisión | Motivo |
|----------|--------|
| Tienda estática, sin framework | Abre con doble clic, carga rápido y se aloja gratis en cualquier servicio de archivos estáticos |
| Copia estática del catálogo generada desde el ERP | La tienda nunca se ve vacía, aunque el ERP esté apagado o no esté en la nube |
| El carrito guarda solo el id y la cantidad | El nombre y el precio se leen siempre del catálogo: un cambio de precio se refleja al instante |
| El ERP calcula los precios y revisa el stock al registrar el pedido | Lo que envía el navegador solo dice qué productos y cuántos; nadie puede cambiar un precio desde su navegador |
| El stock solo cambia con movimientos de inventario | Cada entrada, salida, venta o devolución queda en el historial |
| Fotografías reales en vez de ilustraciones | Un producto de consumo se vende enseñándolo |
| Logotipo oficial en SVG | Escala sin perder nitidez y pesa poco; el mismo archivo sirve para la cabecera y el pie |
| Cifras de mercado con la fuente citada | Un dato sin fuente no se puede sustentar |
| Todas las tarjetas de producto con la misma proporción cuadrada | Es lo que hace que la rejilla se lea ordenada |
| Negro con un solo acento naranja; las pantallas de compra, sobre fondo claro | Es la identidad de JBL, y los formularios y los precios se leen mejor en claro |
| Imágenes con `width`, `height` y `loading="lazy"` | Reservan su espacio antes de cargar, así el contenido no salta |

## Estructura

```
jbl-web/
├── index.html, catalogo.html, producto.html,
│   carrito.html, checkout.html, pedido.html     Páginas de la tienda
├── css/estilos.css           Estilos de la tienda
├── js/
│   ├── tienda.js             Base compartida: catálogo, carrito, cabecera, pie y avisos
│   └── inicio.js, catalogo.js, producto.js, carrito.js,
│       checkout.js, pedido.js, contacto.js      Un archivo por página
├── data/catalogo.js          Copia estática del catálogo (generada, no editar)
├── assets/img/               Logotipo y fotografías (productos, ambiente, secciones)
├── erp/                      ERP en Laravel 12
├── database/schema.sql       Base de datos completa en SQL, con datos de ejemplo
├── scripts/verificar-landing.mjs   Comprobaciones de la tienda
├── jenkins/                  Dockerfile y docker-compose.yml de Jenkins
├── Jenkinsfile               Pipeline de CI/CD
├── .github/                  Plantillas de issues y workflow de CI
├── vercel.json, .vercelignore      Configuración del despliegue
├── docs/                     Documentación y páginas de la wiki
├── trabajo/                  Scripts de aporte del equipo
└── README.md
```

## Reparto del trabajo

| Rama | Integrante | Aporte |
|------|-----------|--------|
| `feature/pagina-inicio` | Diaz Asto, Dietri Josue | Sistema de diseño, navegación, portada, cifras, manifiesto y cierre |
| `feature/interactividad` | Chuco Ortega, Jordy Jorge | Menú móvil, apariciones al hacer scroll y barra fija |
| `feature/galeria-productos` | Huincho Villanueva, Fabrizio Luis | Galería de los seis parlantes con sus fichas técnicas |
| `feature/tecnologia-y-pie` | Jaimes Daza, Julibeth Antonela | Tecnología, resistencia, practicidad y pie de página |
| `feature/erp-base` | Chuco Ortega, Jordy Jorge | Proyecto Laravel, base de datos, inicio de sesión y panel |
| `feature/erp-parlantes` | Huincho Villanueva, Fabrizio Luis | Gestión de parlantes y API del catálogo |
| `feature/erp-mensajes` | Diaz Asto, Dietri Josue | API y bandeja de mensajes, formulario de contacto |
| `feature/erp-usuarios` | Diaz Asto, Dietri Josue | Gestión de administradores |
| `feature/landing-catalogo-erp` | Huincho Villanueva, Fabrizio Luis | La landing muestra el catálogo del ERP |
| `feature/docs-erp` | Diaz Asto, Dietri Josue | Documentación del ERP |

Cada rama se integra en `develop` mediante un pull request revisado y, una vez
probada, se publica en `main`. Las versiones están etiquetadas: `v1.0.0` (la
landing), `v1.1.0` (tres correcciones) y `v1.2.0` (el ERP del catálogo).

La tienda completa, el ERP ampliado y el CI/CD se suben en las cinco ramas que
describe el Anexo A de [`docs/tercer-avance.md`](docs/tercer-avance.md), y
quedarán etiquetados como `v2.0.0`.

## Documentación

- [`erp/README.md`](erp/README.md): instalación y uso del ERP, y detalle de la API.
- [`docs/wiki/`](docs/wiki/): arquitectura, instalación, base de datos, flujo de trabajo, CI/CD y despliegue.
- [`docs/tercer-avance.md`](docs/tercer-avance.md): guía del tercer avance del curso.

## Curso

Trabajo del curso **Herramientas de Desarrollo**, sección 31576, de la Facultad
de Ingeniería de la Universidad Tecnológica del Perú.

## Créditos

El logotipo y las fotografías provienen de Wikimedia Commons (dominio público,
CC0, CC BY y CC BY-SA). La autoría y la licencia de cada archivo, junto con la
fuente de las cifras de mercado, están en
[`docs/creditos-imagenes.md`](docs/creditos-imagenes.md).

Proyecto académico sin fines comerciales. Sitio no oficial: no cobra ni envía
ningún producto. JBL es una marca registrada de Harman International
Industries.
