# Paleta de colores

La landing usa fondo oscuro con **un solo color de acento**. Ese es el criterio:
si todo grita, nada resalta.

## Acento

| Nombre | HEX | Uso |
|--------|-----|-----|
| Naranja JBL | `#ff6a00` | Botón principal, cifras, detalles de los parlantes, línea de la marca |
| Naranja claro | `#ff8c3d` | Sólo para el `hover` del botón sólido |

## Superficies

| Nombre | HEX | Uso |
|--------|-----|-----|
| Fondo | `#0a0a0b` | Fondo general de la página |
| Superficie | `#121215` | Tarjetas y bloques |
| Superficie elevada | `#1a1a1e` | Tarjeta destacada y estados `hover` |
| Borde | `#26262c` | Separadores y contornos |
| Borde fuerte | `#34343c` | Contornos de botones e iconos |

## Texto

| Nombre | HEX | Uso | Contraste sobre el fondo |
|--------|-----|-----|--------------------------|
| Tinta | `#f5f5f3` | Titulares y texto principal | 18:1 |
| Tinta suave | `#9a9aa2` | Texto secundario y etiquetas | 7:1 |
| Naranja JBL | `#ff6a00` | Acento sobre fondo oscuro | 6.9:1 |

Todos superan el mínimo de 4.5:1 que exige la WCAG nivel AA para texto normal.

Los colores se declaran como variables CSS al inicio de `css/estilos.css`,
así que cambiar la marca es cambiar un solo bloque.
