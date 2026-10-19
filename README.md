# JBL Store — Landing publicitaria

Landing de la marca **JBL**, fabricante de parlantes portátiles y equipos de
audio. No es una tienda: presenta la identidad de la marca y muestra la línea
de productos como gancho comercial, sin carrito ni precios.

Incluye un **ERP del catálogo** hecho en Laravel: desde un panel se administran
los parlantes que muestra la landing y se leen los mensajes de su formulario de
contacto.

## Cómo verlo

Abre `index.html` en el navegador. No hace falta servidor ni instalar nada:
si el ERP no está encendido, la página muestra el catálogo escrito en el HTML.

Para ver el catálogo y el formulario conectados al ERP, enciende el ERP
(instrucciones en [`erp/README.md`](erp/README.md)) y sirve la landing en el
puerto 5500:

```
python -m http.server 5500
```

## Tecnologías

| Capa | Tecnología |
|------|------------|
| Estructura | HTML5 semántico |
| Estilos | CSS3 con variables nativas, Flexbox y Grid |
| Interactividad | JavaScript ES6 (IntersectionObserver) |
| Datos de la landing | JSON estático, o la API del ERP cuando está encendido |
| ERP (backend) | Laravel 12 sobre PHP 8.2 |
| Base de datos | MySQL / MariaDB |
| Control de versiones | Git y GitHub |

## Secciones

1. **Portada** — fotografía a sangre con el titular de marca.
2. **Cifras** — datos de mercado de la marca, con su fuente citada.
3. **Marca** — declaración de marca en tipografía display.
4. **Parlantes** — seis modelos con su fotografía y ficha técnica.
5. **Tecnología** — los cuatro rasgos técnicos junto a una fotografía.
6. **Resistencia** — qué significa cada grado de protección IP en la práctica.
7. **Practicidad** — el tamaño frente al volumen, con la comparación de los
   dos extremos de la línea.
8. **Cierre** — llamada a la acción y formulario de contacto.

## ERP del catálogo

| Módulo | Qué permite |
|--------|-------------|
| Inicio | Resumen: parlantes publicados, mensajes sin leer y administradores |
| Parlantes | Agregar, editar, ordenar, publicar u ocultar los parlantes, con su foto |
| Mensajes | Leer, marcar y eliminar los mensajes del formulario de contacto |
| Administradores | Dar o quitar acceso al panel |
| API | `GET /api/parlantes` para la landing y `POST /api/mensajes` para el formulario |

La guía de instalación y uso está en [`erp/README.md`](erp/README.md).

## Decisiones de diseño

| Decisión | Motivo |
|----------|--------|
| Fotografías reales en vez de ilustraciones | Un producto de consumo se vende enseñándolo; las siluetas dibujadas no transmiten el acabado ni el tamaño |
| Logotipo oficial en SVG | Escala sin perder nitidez y pesa menos de 3 KB; el mismo archivo sirve para la barra y el pie |
| Un bloque de dos columnas reutilizable que alterna de lado | Tres secciones seguidas con la foto en el mismo sitio se leen repetitivas |
| Cifras de mercado con la fuente citada | Un dato sin fuente no se puede sustentar; el comunicado de Harman está enlazado |
| Todas las fichas con la misma proporción 4:3 | Es lo que hace que la rejilla se lea ordenada; con fotos de distinto alto el bloque se descuadra |
| Un único ancho de contenedor y un solo valor de separación vertical | Los bordes de cada sección caen siempre en la misma línea |
| Fondo negro con un solo acento naranja | Es la identidad de JBL y deja que manden la fotografía y la tipografía |
| Movimiento reducido a una aparición al hacer scroll | La página se apoya en las imágenes, no en la animación |
| Imágenes con `width`, `height` y `loading="lazy"` | Reservan su espacio antes de cargar, así el contenido no salta |

## Estructura

```
jbl-web/
├── index.html
├── css/estilos.css
├── js/main.js
├── js/catalogo-erp.js    La galería lee el catálogo del ERP
├── js/contacto.js        El formulario envía los mensajes al ERP
├── assets/img/           Fotografías (portada, productos, secciones)
├── data/productos.json
├── docs/                 Documentación del proyecto
├── erp/                  ERP del catálogo (Laravel 12)
├── trabajo/              Scripts de aporte de cada integrante
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

## Curso

Trabajo del curso **Herramientas de Desarrollo**, sección 31576, de la Facultad
de Ingeniería de la Universidad Tecnológica del Perú.

## Créditos

El logotipo y las fotografías provienen de Wikimedia Commons (dominio público,
CC0, CC BY y CC BY-SA). La autoría y la licencia de cada archivo, junto con la
fuente de las cifras de mercado, están en
[`docs/creditos-imagenes.md`](docs/creditos-imagenes.md).

Proyecto académico sin fines comerciales. Sitio no oficial. JBL es una marca
registrada de Harman International Industries.
