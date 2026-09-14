# JBL Store — Landing publicitaria

Landing de la marca **JBL**, fabricante de parlantes portátiles y equipos de
audio. No es una tienda: presenta la identidad de la marca y muestra la línea
de productos como gancho comercial, sin carrito ni precios.

## Cómo verlo

Abre `index.html` en el navegador. No hace falta servidor ni instalar nada.

## Tecnologías

| Capa | Tecnología |
|------|------------|
| Estructura | HTML5 semántico |
| Estilos | CSS3 con variables nativas, Flexbox y Grid |
| Interactividad | JavaScript ES6 (IntersectionObserver) |
| Datos | JSON estático |
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
8. **Cierre** — llamada a la acción sobre imagen a sangre.

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
├── assets/img/           Fotografías (portada, productos, secciones)
├── data/productos.json
├── docs/                 Documentación del proyecto
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

Cada rama sale de `feature/pagina-inicio`, se integra en `develop` mediante una
fusión `--no-ff` y, una vez probada, se publica en `main`. La versión estable
está etiquetada como `v1.0.0`.

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
