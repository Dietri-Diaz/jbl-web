# Instalación y ejecución

## Requisitos

| Herramienta | Versión | Para qué |
|-------------|---------|----------|
| Git | 2.40 o superior | Clonar el repositorio y trabajar con ramas |
| PHP | 8.2 o superior (el de XAMPP sirve), con la extensión GD | ERP |
| Composer | 2 | Dependencias del ERP |
| MySQL o MariaDB | MySQL 8 / MariaDB 10.4 (el de XAMPP sirve) | Base de datos del ERP |
| Node.js | 20 o superior | Solo para ejecutar las comprobaciones de la tienda |
| Python 3 (opcional) | cualquiera | Servir la tienda en un puerto local |

## 1. Clonar el repositorio

```bash
git clone https://github.com/Dietri-Diaz/jbl-web.git
cd jbl-web
git switch develop
```

## 2. Ver la tienda

Opción rápida: abrir `index.html` con doble clic. Funciona sin instalar nada: usa la copia del catálogo de `data/catalogo.js`, y los pedidos son de demostración (se guardan solo en el navegador, con un código `DEMO-xxxxxx`).

Para verla conectada al ERP, servirla en el puerto 5500 desde la raíz del repositorio:

```bash
python -m http.server 5500
```

Y abrir http://127.0.0.1:5500. Con el ERP encendido (pasos 3 a 5), la tienda muestra los precios y el stock de la base de datos, y los pedidos quedan registrados.

## 3. Instalar el ERP

Desde la carpeta `erp/`:

```bash
cd erp
composer install
cp .env.example .env
php artisan key:generate
```

En la consola de Windows (CMD), el tercer comando es `copy .env.example .env`.

## 4. Crear la base de datos

Encender MySQL desde el panel de XAMPP y elegir una de las dos formas.

**Con Laravel** (recomendada): crear una base vacía llamada `jbl_erp` en phpMyAdmin y ejecutar:

```bash
php artisan migrate --seed
```

**Con el script SQL** (no necesita Laravel): desde la raíz del repositorio:

```bash
mysql -u root -p < database/schema.sql
```

El script crea la base `jbl_erp`, sus 16 tablas y los datos de ejemplo. Si la base ya existía, borra sus tablas y las vuelve a crear.

Las dos formas dejan los mismos datos: 3 usuarios, 5 categorías, 26 productos, 22 pedidos, 5 mensajes y la bitácora. La diferencia es que el seeder de Laravel además copia las fotos de los productos a `erp/public/uploads/productos/`, que es de donde las sirve el ERP. Si se usó el script SQL y el panel no muestra las fotos, ejecutar `php artisan migrate:fresh --seed`.

## 5. Encender el ERP

```bash
php artisan serve
```

Abrir http://127.0.0.1:8000 e ingresar con una de las cuentas de demostración:

| Correo | Rol | Contraseña |
|--------|-----|------------|
| `admin@jbl.pe` | Administrador: todo | `admin123` |
| `editor@jbl.pe` | Editor: todo menos Usuarios y Bitácora | `admin123` |
| `consulta@jbl.pe` | Solo lectura: mira, pero no modifica | `admin123` |

## 6. Probar el recorrido completo

Con el ERP encendido y la tienda en el puerto 5500:

1. En la tienda, agregar productos al carrito y finalizar la compra. La confirmación muestra un código `JBL-0000NN`.
2. En el panel, abrir **Pedidos**: el pedido nuevo aparece como Pendiente, y en **Movimientos** está la venta que descontó el stock.
3. Cambiar el estado del pedido en el panel.
4. En la tienda, abrir `pedido.html` y escribir el código y el correo: se ve el estado nuevo.

Es una tienda de demostración: no se cobra ni se envía nada.

## 7. Actualizar la copia estática del catálogo

Después de cambiar productos o precios en el panel, desde `erp/`:

```bash
php artisan tienda:exportar
```

Vuelve a escribir `data/catalogo.js`, que es lo que usa la tienda cuando el ERP no está disponible (por ejemplo, publicada en Vercel). Ese archivo no se edita a mano.

## 8. Ejecutar las comprobaciones

Son las mismas que ejecuta el pipeline.

```bash
# Desde la raíz: enlaces de las 6 páginas, catálogo, fotos y sintaxis de JavaScript
node scripts/verificar-landing.mjs

# Desde erp/: 46 pruebas del ERP (usan SQLite en memoria, no tocan jbl_erp)
php artisan test
```

## Problemas frecuentes

| Problema | Causa | Solución |
|----------|-------|----------|
| `SQLSTATE[HY000] [2002]` al migrar | MySQL está apagado | Encenderlo en el panel de XAMPP |
| `Unknown database 'jbl_erp'` | Falta crear la base | Crearla en phpMyAdmin o usar `database/schema.sql` |
| La tienda no muestra los cambios hechos en el panel | El ERP está apagado y la tienda usa la copia estática | Encender el ERP con `php artisan serve` y recargar la página |
| El pedido sale con código `DEMO-` y no aparece en el panel | La tienda no encontró el ERP | Encender el ERP y recargar antes de finalizar la compra |
| `No application encryption key has been specified` | Falta la clave del `.env` | `php artisan key:generate` |
| Las fotos de los productos no aparecen en el panel | No se ejecutó el seeder, que es el que copia las fotos | `php artisan migrate:fresh --seed` |
| Las pruebas fallan con un error de GD | Falta la extensión GD de PHP | Activar `extension=gd` en `php.ini` |
| El panel responde «Tu rol es de solo lectura» | Se ingresó con `consulta@jbl.pe` | Ingresar con `admin@jbl.pe` o `editor@jbl.pe` |
