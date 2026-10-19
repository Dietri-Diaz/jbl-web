# JBL ERP — Panel del catálogo

Panel de administración de la landing de JBL, hecho con **Laravel 12**. Desde
aquí se decide qué parlantes muestra la landing, en qué orden y con qué ficha
técnica, y se leen los mensajes que llegan desde su formulario de contacto.

## Módulos

| Módulo | Qué permite |
|--------|-------------|
| **Inicio** | Resumen: parlantes publicados, mensajes sin leer, administradores y últimos mensajes |
| **Parlantes** | Agregar, editar y eliminar parlantes con su foto; publicarlos u ocultarlos en la landing; ordenarlos y destacarlos con un sello |
| **Mensajes** | Leer los mensajes del formulario de contacto, marcarlos como leídos o no leídos, responder por correo y eliminarlos |
| **Administradores** | Dar o quitar acceso al panel. Nadie puede eliminar su propia cuenta y siempre queda al menos un administrador |

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
con los datos iniciales:

```bash
php artisan migrate --seed
```

Los datos iniciales son un administrador y los seis parlantes que ya muestra la
landing, con sus mismas fotos.

## Uso

```bash
php artisan serve
```

Abre <http://127.0.0.1:8000> e ingresa con:

| Correo | Contraseña |
|--------|------------|
| `admin@jbl.pe` | `admin123` |

Cambia esa contraseña desde **Administradores** la primera vez que ingreses.

## Cómo se conecta con la landing

La landing no necesita el ERP para funcionar: si el ERP está apagado, muestra el
catálogo escrito en `index.html`. Cuando está encendido:

- `js/catalogo-erp.js` pide `GET /api/parlantes` y arma la galería con los
  parlantes publicados, en su orden.
- `js/contacto.js` envía el formulario a `POST /api/mensajes`, y el mensaje
  aparece en la bandeja del panel.

Para probarlo, sirve la landing en el puerto 5500 desde la raíz del repositorio:

```bash
python -m http.server 5500
```

La dirección del ERP que usa la landing se puede cambiar con el atributo
`data-erp` de la etiqueta `<html>`, por ejemplo `<html lang="es" data-erp="https://erp.ejemplo.com">`.

## API

| Método y ruta | Uso | Respuestas |
|---------------|-----|------------|
| `GET /api/parlantes` | Parlantes publicados, en su orden | `200` con `{ "data": [...] }` |
| `POST /api/mensajes` | Guarda un mensaje del formulario (`nombre`, `correo`, `mensaje`) | `201` guardado · `422` datos inválidos · `429` más de 5 envíos por minuto |

## Estructura

```
erp/
├── app/Http/Controllers/        Controladores del panel y de la API (Api/)
├── app/Models/                  Parlante, Mensaje y User
├── database/migrations/         Tablas parlantes y mensajes
├── database/seeders/            Administrador y catálogo inicial
├── lang/es/validation.php       Mensajes de validación en español
├── public/css/erp.css           Estilos del panel
├── resources/views/erp/         Vistas del panel
├── routes/erp/                  Rutas de cada módulo del panel
└── routes/api/                  Rutas de la API pública
```

Cada módulo tiene su propio archivo de rutas en `routes/erp/` o `routes/api/`.
Así cada integrante agregó el suyo sin tocar los archivos de los demás.
