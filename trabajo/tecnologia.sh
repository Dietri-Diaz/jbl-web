#!/usr/bin/env bash
# ==========================================================================
#  PROYECTO JBL  -  aporte de JULIBETH
#  Rama: feature/tecnologia-y-pie
#
#  Ejecutalo desde la carpeta del repositorio:
#      bash trabajo/tecnologia.sh
# ==========================================================================
set -e

RAMA="feature/tecnologia-y-pie"

[ -d .git ] || {
  echo "ERROR: ejecuta esto dentro de la carpeta jbl-web."
  exit 1
}

echo ""
echo "=================================================================="
echo "   Aporte de JULIBETH  ->  $RAMA"
echo "=================================================================="
echo ""

# ---- Tu identidad ----------------------------------------------------------
limpiar() {
  printf '%s' "$1" | tr -d '\r\n' | sed -e 's/^\xEF\xBB\xBF//' \
    -e 's/^[[:space:]]*//' -e 's/[[:space:]]*$//'
}

NOMBRE=$(limpiar "$(git config user.name || true)")
CORREO=$(limpiar "$(git config user.email || true)")

if [ -z "$NOMBRE" ] || [ -z "$CORREO" ]; then
  echo "Antes de continuar, dinos quien eres:"
  printf "   Nombre y apellido    : "
  read -r NOMBRE
  printf "   Correo de tu GitHub  : "
  read -r CORREO
  NOMBRE=$(limpiar "$NOMBRE")
  CORREO=$(limpiar "$CORREO")
  git config --local user.name  "$NOMBRE"
  git config --local user.email "$CORREO"
  echo ""
fi

echo "Tus commits van a quedar como: $NOMBRE <$CORREO>"
echo ""

# ---- Tu rama ---------------------------------------------------------------
git fetch origin --prune
if git rev-parse --verify --quiet "origin/$RAMA" > /dev/null; then
  git switch -C "$RAMA" "origin/$RAMA"
else
  git switch -C "$RAMA" "origin/feature/pagina-inicio"
fi
echo ""

# ---- Tus commits -----------------------------------------------------------
echo "--> Tecnologia, resistencia, practicidad y pie de pagina"
cat > css/estilos.css <<'FIN_DE_ARCHIVO_JBL'
/* =========================================================================
   JBL — Landing publicitaria
   Fondo oscuro con un solo color de acento. Las fotografías mandan; el
   movimiento se limita a una aparición suave al hacer scroll.
   ========================================================================= */

/* ------------------------------ 1. TOKENS ------------------------------ */

:root {
  /* Color */
  --fondo:        #0a0a0b;
  --superficie:   #141417;
  --superficie-2: #1c1c21;
  --borde:        #26262c;
  --borde-fuerte: #3a3a43;

  --tinta:        #f5f5f3;
  --tinta-suave:  #a8a8b0;

  --acento:       #ff6a00;
  --acento-claro: #ff8c3d;

  /* Tipografía */
  --display: "Bebas Neue", "Arial Narrow", Impact, sans-serif;
  --texto:   "Source Sans 3", -apple-system, "Segoe UI", Roboto, sans-serif;

  /* Ritmo vertical: TODAS las secciones usan el mismo valor. De ahí viene
     la sensación de orden; mezclar separaciones es lo que descuadra. */
  --seccion:   clamp(4.5rem, 9vw, 7.5rem);
  --contenedor: 1200px;
  --margen:    clamp(1.25rem, 5vw, 3rem);

  /* Escala de espaciado */
  --e-xs: 0.35rem;
  --e-sm: 0.75rem;
  --e-md: 1.25rem;
  --e-lg: 2rem;
  --e-xl: 3rem;
  --e-2xl: 4.5rem;

  --radio: 6px;

  /* Movimiento */
  --curva: cubic-bezier(0.22, 1, 0.36, 1);
  --rapido: 180ms;
  --medio: 320ms;

  --z-fondo: 1;
  --z-contenido: 10;
  --z-nav: 30;

  color-scheme: dark;
}

/* ------------------------------ 2. BASE ------------------------------ */

*, *::before, *::after { box-sizing: border-box; }
* { margin: 0; padding: 0; }

html {
  scroll-behavior: smooth;
  scroll-padding-top: 5rem;
  -webkit-text-size-adjust: 100%;
}

body {
  background: var(--fondo);
  color: var(--tinta);
  font-family: var(--texto);
  font-size: 1.0625rem;
  line-height: 1.65;
  overflow-x: hidden;
  -webkit-font-smoothing: antialiased;
}

img { display: block; max-width: 100%; height: auto; }
svg { display: block; }
a { color: inherit; text-decoration: none; }
ul { list-style: none; }

.sprite { position: absolute; width: 0; height: 0; overflow: hidden; }

:focus-visible {
  outline: 2px solid var(--acento);
  outline-offset: 3px;
  border-radius: 2px;
}

.salto {
  position: absolute;
  left: var(--e-md);
  top: -100px;
  z-index: 100;
  padding: 0.75rem 1.25rem;
  background: var(--acento);
  color: #0a0a0b;
  font-weight: 700;
  transition: top var(--rapido) var(--curva);
}
.salto:focus { top: var(--e-md); }

/* ------------------------------ 3. ESTRUCTURA ------------------------------ */

/* Un único ancho de contenedor para toda la página: así los bordes de
   cada sección caen siempre en la misma línea vertical. */
.contenedor {
  width: 100%;
  max-width: var(--contenedor);
  margin-inline: auto;
  padding-inline: var(--margen);
}

.seccion { padding-block: var(--seccion); }

.cabecera {
  display: flex;
  flex-direction: column;
  gap: var(--e-sm);
  max-width: 46ch;
  margin-bottom: var(--e-2xl);
}
.cabecera__texto {
  color: var(--tinta-suave);
  text-wrap: pretty;
}

/* ------------------------------ 4. TIPOGRAFÍA ------------------------------ */

.etiqueta {
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  color: var(--acento);
}

.titulo {
  font-family: var(--display);
  font-size: clamp(2.75rem, 6.5vw, 5rem);
  line-height: 0.92;
  letter-spacing: 0.01em;
  text-transform: uppercase;
  text-wrap: balance;
}

.acento { color: var(--acento); }

/* ------------------------------ 5. BOTONES ------------------------------ */

.acciones {
  display: flex;
  flex-wrap: wrap;
  gap: var(--e-md);
}

.boton {
  display: inline-flex;
  align-items: center;
  gap: 0.6rem;
  padding: 0.95rem 1.7rem;
  font-size: 0.9375rem;
  font-weight: 700;
  letter-spacing: 0.02em;
  border-radius: var(--radio);
  border: 2px solid transparent;
  cursor: pointer;
  transition: background-color var(--rapido) var(--curva),
              border-color var(--rapido) var(--curva),
              color var(--rapido) var(--curva);
}

.boton--solido { background: var(--acento); color: #0a0a0b; }
.boton--solido:hover { background: var(--acento-claro); }

.boton--linea { border-color: rgba(245, 245, 243, 0.35); color: var(--tinta); }
.boton--linea:hover {
  border-color: var(--tinta);
  background: rgba(245, 245, 243, 0.08);
}

.boton--grande { padding: 1.15rem 2.2rem; font-size: 1rem; }

.boton__ico {
  width: 18px;
  height: 18px;
  fill: none;
  stroke: currentColor;
  stroke-width: 2.2;
  stroke-linecap: round;
  stroke-linejoin: round;
  transition: transform var(--rapido) var(--curva);
}
.boton:hover .boton__ico { transform: translateX(4px); }

/* ------------------------------ 6. APARICIÓN ------------------------------ */

/* Sólo se esconde si el navegador ejecuta JavaScript; sin él, la página
   se ve completa. */
.reveal {
  opacity: 0;
  transform: translateY(16px);
  transition: opacity var(--medio) var(--curva),
              transform var(--medio) var(--curva);
}
.reveal.visible { opacity: 1; transform: none; }

/* ------------------------------ 7. NAVEGACIÓN ------------------------------ */

.nav {
  position: fixed;
  inset: 0 0 auto 0;
  z-index: var(--z-nav);
  border-bottom: 1px solid transparent;
  transition: background-color var(--medio) var(--curva),
              border-color var(--medio) var(--curva);
}
.nav.compacta {
  background: rgba(10, 10, 11, 0.85);
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
  border-bottom-color: var(--borde);
}

.nav__caja {
  max-width: var(--contenedor);
  margin-inline: auto;
  padding: 1.1rem var(--margen);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: var(--e-lg);
}

/* El logotipo es el oficial, en SVG: escala sin perder nitidez. */
.marca { display: inline-flex; align-items: center; }
.marca img { width: 64px; height: auto; }

.nav__menu { display: flex; gap: var(--e-xl); font-size: 0.9375rem; font-weight: 600; }
.nav__menu a { color: var(--tinta-suave); transition: color var(--rapido) var(--curva); }
.nav__menu a:hover { color: var(--tinta); }

.nav__boton {
  display: none;
  flex-direction: column;
  justify-content: center;
  gap: 5px;
  width: 44px;
  height: 44px;
  background: none;
  border: 1px solid var(--borde-fuerte);
  border-radius: var(--radio);
  cursor: pointer;
}
.nav__boton span {
  display: block;
  width: 18px;
  height: 2px;
  margin-inline: auto;
  background: var(--tinta);
  transition: transform var(--rapido) var(--curva);
}
.nav__boton[aria-expanded="true"] span:first-child { transform: translateY(3.5px) rotate(45deg); }
.nav__boton[aria-expanded="true"] span:last-child  { transform: translateY(-3.5px) rotate(-45deg); }

/* ------------------------------ 8. PORTADA ------------------------------ */

.portada {
  position: relative;
  min-height: min(92svh, 820px);
  display: flex;
  align-items: flex-end;
  padding-block: calc(var(--seccion) + 4rem) var(--seccion);
  overflow: hidden;
}

/* La foto va detrás, a sangre, con un velo que garantiza el contraste. */
.portada__fondo {
  position: absolute;
  inset: 0;
  z-index: var(--z-fondo);
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
}
.portada::after {
  content: "";
  position: absolute;
  inset: 0;
  z-index: var(--z-fondo);
  background:
    linear-gradient(to right, rgba(10,10,11,.92) 0%, rgba(10,10,11,.62) 55%, rgba(10,10,11,.45) 100%),
    linear-gradient(to top, var(--fondo) 2%, transparent 45%);
}

.portada__contenido {
  position: relative;
  z-index: var(--z-contenido);
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: var(--e-md);
}

.portada__titulo {
  font-family: var(--display);
  font-size: clamp(3.25rem, 10vw, 8rem);
  line-height: 0.86;
  letter-spacing: 0.01em;
  text-transform: uppercase;
}

.portada__bajada {
  max-width: 46ch;
  font-size: 1.125rem;
  color: var(--tinta-suave);
  text-wrap: pretty;
}

/* ------------------------------ 9. CIFRAS ------------------------------ */

.cifras {
  border-block: 1px solid var(--borde);
  background: var(--superficie);
}
.cifras__rejilla {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: var(--e-lg);
  padding-block: var(--e-xl);
}
.cifra { text-align: center; }
.cifra__dato {
  font-family: var(--display);
  font-size: clamp(2.25rem, 4.5vw, 3.25rem);
  line-height: 1;
  letter-spacing: 0.02em;
  font-variant-numeric: tabular-nums;
  color: var(--acento);
}
/* La unidad va debajo y más pequeña: la cifra se lee de un golpe. */
.cifra__unidad {
  display: block;
  margin-top: 0.1rem;
  font-family: var(--texto);
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 0.16em;
  text-transform: uppercase;
  color: var(--tinta-suave);
}
.cifra__pie {
  margin-top: var(--e-sm);
  max-width: 22ch;
  margin-inline: auto;
  font-size: 0.8125rem;
  line-height: 1.45;
  color: var(--tinta-suave);
  text-wrap: pretty;
}
.cifras__fuente {
  margin-top: var(--e-lg);
  padding-top: var(--e-md);
  border-top: 1px solid var(--borde);
  font-size: 0.75rem;
  line-height: 1.5;
  color: var(--tinta-suave);
  text-align: center;
}

/* ------------------------------ 10. MARCA ------------------------------ */

.bloque-marca {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: var(--e-md);
  text-align: center;
}
.declaracion {
  max-width: 30ch;
  font-family: var(--display);
  font-size: clamp(2.25rem, 5.5vw, 4.25rem);
  line-height: 1.02;
  letter-spacing: 0.012em;
  text-transform: uppercase;
  text-wrap: balance;
}
.declaracion strong { color: var(--acento); font-weight: 400; }
.firma {
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  color: var(--tinta-suave);
}

/* MARCA: estilos de la galeria -> feature/galeria-productos */

/* ------------------------------ 12. TECNOLOGÍA ------------------------------ */

.tecnologia { background: var(--superficie); border-block: 1px solid var(--borde); }
.practicidad { background: var(--superficie); border-block: 1px solid var(--borde); }

/* Bloque de dos columnas reutilizable: foto a un lado, texto al otro.
   Las secciones alternan el lado con .duo--invertido. */
.duo {
  display: grid;
  grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
  gap: clamp(2rem, 5vw, 4rem);
  align-items: center;
}

.duo__foto {
  border-radius: var(--radio);
  overflow: hidden;
  border: 1px solid var(--borde);
}
.duo__foto img { width: 100%; aspect-ratio: 4 / 3.4; object-fit: cover; }

.duo__texto { display: flex; flex-direction: column; gap: var(--e-sm); }

@media (min-width: 1001px) {
  .duo--invertido .duo__texto { order: -1; }
}
.duo__parrafo {
  max-width: 52ch;
  color: var(--tinta-suave);
  text-wrap: pretty;
}
.duo__parrafo:first-of-type { margin-top: var(--e-xs); }

/* Lista de definiciones para los grados de protección */
.datos {
  margin-top: var(--e-md);
  border-top: 1px solid var(--borde);
}
.datos__fila {
  display: grid;
  grid-template-columns: 5.5rem minmax(0, 1fr);
  gap: var(--e-md);
  padding: var(--e-sm) 0;
  border-bottom: 1px solid var(--borde);
}
.datos__fila dt {
  font-family: var(--display);
  font-size: 1.35rem;
  line-height: 1.1;
  letter-spacing: 0.04em;
  color: var(--acento);
}
.datos__fila dd {
  font-size: 0.9375rem;
  color: var(--tinta-suave);
  text-wrap: pretty;
}

/* Comparación de los dos extremos de la línea */
.comparativa {
  display: grid;
  grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
  align-items: center;
  gap: var(--e-md);
  margin-top: var(--e-lg);
  padding: var(--e-md);
  border: 1px solid var(--borde);
  border-radius: var(--radio);
  background: var(--fondo);
}
.comparativa__polo { text-align: center; }
.comparativa__valor {
  font-family: var(--display);
  font-size: 2rem;
  line-height: 1;
  letter-spacing: 0.02em;
  font-variant-numeric: tabular-nums;
}
.comparativa__polo--fuerte .comparativa__valor { color: var(--acento); }
.comparativa__nombre {
  margin-top: 0.2rem;
  font-size: 0.8125rem;
  font-weight: 700;
}
.comparativa__nota {
  font-size: 0.75rem;
  color: var(--tinta-suave);
}
.comparativa__barra {
  width: clamp(2rem, 6vw, 4.5rem);
  height: 2px;
  border-radius: 2px;
  background: linear-gradient(to right, var(--borde-fuerte), var(--acento));
}

.rasgos {
  display: flex;
  flex-direction: column;
  gap: var(--e-md);
  margin-top: var(--e-md);
}
.rasgo {
  display: grid;
  grid-template-columns: auto minmax(0, 1fr);
  gap: var(--e-md);
  align-items: start;
  padding-top: var(--e-md);
  border-top: 1px solid var(--borde);
}
.rasgo:first-child { padding-top: 0; border-top: 0; }
.rasgo__ico {
  width: 26px;
  height: 26px;
  margin-top: 0.2rem;
  color: var(--acento);
}
.rasgo h3 {
  font-family: var(--display);
  font-size: 1.5rem;
  line-height: 1.1;
  letter-spacing: 0.02em;
  text-transform: uppercase;
  font-weight: 400;
}
.rasgo p {
  margin-top: 0.15rem;
  font-size: 0.9375rem;
  color: var(--tinta-suave);
  text-wrap: pretty;
}


/* ------------------------------ 13. CIERRE ------------------------------ */

.cierre {
  position: relative;
  padding-block: calc(var(--seccion) * 1.15);
  overflow: hidden;
}
.cierre__fondo {
  position: absolute;
  inset: 0;
  z-index: var(--z-fondo);
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.cierre::after {
  content: "";
  position: absolute;
  inset: 0;
  z-index: var(--z-fondo);
  background: linear-gradient(to right, rgba(10,10,11,.94) 10%, rgba(10,10,11,.55) 100%);
}
.cierre__contenido {
  position: relative;
  z-index: var(--z-contenido);
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: var(--e-md);
}
.cierre__titulo {
  font-family: var(--display);
  font-size: clamp(2.75rem, 8vw, 6.5rem);
  line-height: 0.88;
  letter-spacing: 0.01em;
  text-transform: uppercase;
}
.cierre__texto {
  max-width: 44ch;
  font-size: 1.125rem;
  color: var(--tinta-suave);
  text-wrap: pretty;
}

/* ------------------------------ 14. PIE ------------------------------ */

.pie {
  padding-block: var(--e-2xl) var(--e-xl);
  border-top: 1px solid var(--borde);
  background: var(--superficie);
}

/* La columna de marca pesa más que las de enlaces: por eso 1.6fr. */
.pie__rejilla {
  display: grid;
  grid-template-columns: 1.6fr repeat(3, 1fr);
  gap: clamp(2rem, 4vw, 3.5rem);
  padding-bottom: var(--e-xl);
  border-bottom: 1px solid var(--borde);
}

.pie__marca {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: var(--e-md);
}
.pie__marca .marca img { width: 88px; }

.pie__resumen {
  max-width: 42ch;
  font-size: 0.9375rem;
  line-height: 1.6;
  color: var(--tinta-suave);
  text-wrap: pretty;
}

/* Redes sociales */
.redes {
  display: flex;
  gap: var(--e-sm);
  margin-top: var(--e-xs);
}
.redes a {
  display: grid;
  place-items: center;
  width: 42px;
  height: 42px;
  border: 1px solid var(--borde);
  border-radius: var(--radio);
  color: var(--tinta-suave);
  transition: color var(--rapido) var(--curva),
              border-color var(--rapido) var(--curva),
              background-color var(--rapido) var(--curva);
}
.redes a:hover {
  color: #0a0a0b;
  background: var(--acento);
  border-color: var(--acento);
}
.redes__ico {
  width: 18px;
  height: 18px;
  fill: currentColor;
}

/* Columnas de enlaces */
.pie__columna { display: flex; flex-direction: column; gap: var(--e-sm); }
.pie__titulo {
  font-family: var(--display);
  font-size: 1.15rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  font-weight: 400;
  color: var(--tinta);
}
.pie__columna ul { display: flex; flex-direction: column; gap: 0.45rem; }
.pie__columna a {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.9375rem;
  color: var(--tinta-suave);
  transition: color var(--rapido) var(--curva);
}
.pie__columna a:hover { color: var(--acento); }
.pie__ico {
  width: 13px;
  height: 13px;
  fill: none;
  stroke: currentColor;
  stroke-width: 2;
  stroke-linecap: round;
  stroke-linejoin: round;
  opacity: 0.65;
}

/* Aviso legal */
.pie__barra {
  display: flex;
  flex-wrap: wrap;
  align-items: flex-start;
  justify-content: space-between;
  gap: var(--e-md);
  padding-top: var(--e-lg);
}
.pie__aviso {
  flex: 1 1 42ch;
  max-width: 82ch;
  font-size: 0.8125rem;
  line-height: 1.65;
  color: var(--tinta-suave);
  text-wrap: pretty;
}
.pie__aviso strong { color: var(--tinta); font-weight: 600; }
.pie__aviso code {
  font-family: ui-monospace, Consolas, monospace;
  font-size: 0.95em;
  color: var(--tinta);
}
.pie__firma {
  flex: 0 0 auto;
  font-size: 0.75rem;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--tinta-suave);
}


/* ------------------------------ 15. RESPONSIVE ------------------------------ */

@media (max-width: 1000px) {
  .galeria { grid-template-columns: repeat(2, 1fr); }
  .pie__rejilla { grid-template-columns: repeat(2, 1fr); }
  .pie__marca { grid-column: 1 / -1; }
  .cifras__rejilla { grid-template-columns: repeat(2, 1fr); row-gap: var(--e-xl); }

  /* En una sola columna la foto siempre va arriba, sin importar el orden
     que tuviera en el escritorio. */
  .duo { grid-template-columns: minmax(0, 1fr); }
  .duo__foto { order: -1; }
}

@media (max-width: 860px) {
  .nav__boton { display: flex; }

  .nav__menu {
    position: absolute;
    inset: 100% var(--margen) auto var(--margen);
    flex-direction: column;
    gap: 0;
    padding: var(--e-xs) 0;
    background: var(--superficie);
    border: 1px solid var(--borde);
    border-radius: var(--radio);
    opacity: 0;
    transform: translateY(-8px);
    transform-origin: top center;
    pointer-events: none;
    transition: opacity var(--rapido) var(--curva),
                transform var(--rapido) var(--curva);
  }
  .nav__menu.abierto { opacity: 1; transform: none; pointer-events: auto; }
  .nav__menu a { padding: 0.8rem var(--e-md); }

  .portada { min-height: auto; padding-block: calc(var(--seccion) + 3rem) var(--seccion); }
  .portada::after {
    background:
      linear-gradient(to right, rgba(10,10,11,.9) 0%, rgba(10,10,11,.72) 100%),
      linear-gradient(to top, var(--fondo) 2%, transparent 50%);
  }
}

@media (max-width: 560px) {
  .galeria { grid-template-columns: minmax(0, 1fr); }
  .pie__rejilla { grid-template-columns: minmax(0, 1fr); }
  .pie__marca { grid-column: auto; }
  .cifras__rejilla { grid-template-columns: minmax(0, 1fr); }
  .comparativa { grid-template-columns: minmax(0, 1fr); }
  .comparativa__barra { width: 100%; }
  .datos__fila { grid-template-columns: minmax(0, 1fr); gap: 0.2rem; }
  .boton { width: 100%; justify-content: center; }
  .acciones { width: 100%; }
  .declaracion { max-width: 100%; }
}

/* ------------------------------ 16. MOVIMIENTO REDUCIDO ------------------------------ */

@media (prefers-reduced-motion: reduce) {
  html { scroll-behavior: auto; }
  *, *::before, *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
  .reveal { opacity: 1; transform: none; }
  .tarjeta:hover .tarjeta__foto img { transform: none; }
}
FIN_DE_ARCHIVO_JBL
cat > index.html <<'FIN_DE_ARCHIVO_JBL'
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>JBL — El sonido que se siente</title>
  <meta name="description" content="Conoce la línea de parlantes portátiles JBL: JBL Pro Sound, resistencia al agua y hasta 24 horas de batería. Del Go 2 de bolsillo al Boombox 3 de 180 vatios.">
  <meta name="theme-color" content="#0a0a0b">

  <meta property="og:type" content="website">
  <meta property="og:title" content="JBL — El sonido que se siente">
  <meta property="og:description" content="Parlantes portátiles con JBL Pro Sound. Del Go 2 de bolsillo al Boombox 3 de 180 vatios.">
  <meta property="og:image" content="assets/img/hero-marca.jpg">
  <meta property="og:locale" content="es_PE">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Source+Sans+3:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<a class="salto" href="#contenido">Saltar al contenido</a>

<!-- Iconos de trazo, estilo Lucide. Se definen una vez y se reutilizan. -->
<svg class="sprite" aria-hidden="true" focusable="false">
  <symbol id="ico-sonido" viewBox="0 0 24 24"><path d="M11 5 6 9H2v6h4l5 4V5Z"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/></symbol>
  <symbol id="ico-agua" viewBox="0 0 24 24"><path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5s-3.5-4-4-6.5c-.5 2.5-2 4.9-4 6.5S5 13 5 15a7 7 0 0 0 7 7Z"/></symbol>
  <symbol id="ico-bateria" viewBox="0 0 24 24"><rect x="2" y="7" width="16" height="10" rx="2"/><path d="M22 11v2"/><path d="M6 11v2"/><path d="M10 11v2"/></symbol>
  <symbol id="ico-enlace" viewBox="0 0 24 24"><path d="M4.93 19.07a10 10 0 0 1 0-14.14"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/><path d="M7.76 16.24a6 6 0 0 1 0-8.49"/><path d="M16.24 7.76a6 6 0 0 1 0 8.49"/><circle cx="12" cy="12" r="2"/></symbol>
  <symbol id="ico-flecha" viewBox="0 0 24 24"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></symbol>
  <symbol id="ico-externo" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><path d="M15 3h6v6"/><path d="M10 14 21 3"/></symbol>
</svg>

<!-- Logotipos de las redes sociales. Van rellenos, no de trazo, que es como
     se dibujan las marcas y como se leen mejor en tamaño pequeño. -->
<svg class="sprite" aria-hidden="true" focusable="false">
  <symbol id="red-facebook" viewBox="0 0 24 24"><path d="M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 0 1 1.141.195v3.325a8.623 8.623 0 0 0-.653-.036 26.805 26.805 0 0 0-.733-.009c-.707 0-1.259.096-1.675.309a1.686 1.686 0 0 0-.679.622c-.258.42-.374.995-.374 1.752v1.297h3.919l-.386 2.103-.287 1.564h-3.246v8.245C19.396 23.238 24 18.179 24 12.044c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.628 3.874 10.35 9.101 11.647Z"/></symbol>
  <symbol id="red-instagram" viewBox="0 0 24 24"><path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 1 0 0-12.324zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405a1.441 1.441 0 0 1-2.88 0 1.44 1.44 0 0 1 2.88 0z"/></symbol>
  <symbol id="red-x" viewBox="0 0 24 24"><path d="M18.901 1.153h3.68l-8.04 9.19L24 22.846h-7.406l-5.8-7.584-6.638 7.584H.474l8.6-9.83L0 1.154h7.594l5.243 6.932ZM17.61 20.644h2.039L6.486 3.24H4.298Z"/></symbol>
  <symbol id="red-youtube" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></symbol>
</svg>

<!-- ================= NAVEGACIÓN ================= -->
<header class="nav" id="nav">
  <div class="nav__caja">
    <a class="marca" href="#inicio" aria-label="JBL, ir al inicio">
      <img src="assets/img/logo-jbl.svg" alt="JBL" width="64" height="45">
    </a>

    <nav class="nav__menu" id="menu" aria-label="Navegación principal">
      <a href="#parlantes">Parlantes</a>
      <a href="#tecnologia">Tecnología</a>
      <a href="#resistencia">Resistencia</a>
      <a href="#contacto">Contacto</a>
    </nav>

    <button class="nav__boton" id="navBoton" aria-label="Abrir menú" aria-expanded="false" aria-controls="menu">
      <span aria-hidden="true"></span>
      <span aria-hidden="true"></span>
    </button>
  </div>
</header>

<main id="contenido">

  <!-- ================= PORTADA ================= -->
  <section class="portada" id="inicio">
    <img class="portada__fondo" src="assets/img/hero-marca.jpg" alt="" aria-hidden="true">
    <div class="contenedor portada__contenido">
      <p class="etiqueta reveal">Audio portátil · Desde 1946</p>
      <h1 class="portada__titulo reveal">
        El sonido<br>
        <span class="acento">que se siente</span>
      </h1>
      <p class="portada__bajada reveal">
        Graves que empujan el pecho, agudos que no se rompen y una carcasa que
        aguanta la playa, la lluvia y la fiesta.
      </p>
      <div class="acciones reveal">
        <a class="boton boton--solido" href="#parlantes">
          Ver los parlantes
          <svg class="boton__ico" aria-hidden="true"><use href="#ico-flecha"/></svg>
        </a>
        <a class="boton boton--linea" href="#marca">Conocer la marca</a>
      </div>
    </div>
  </section>

  <!-- ================= CIFRAS ================= -->
  <section class="cifras" aria-label="La marca en cifras">
    <div class="contenedor">
      <div class="cifras__rejilla">
        <div class="cifra">
          <p class="cifra__dato">100<span class="cifra__unidad">millones</span></p>
          <p class="cifra__pie">Parlantes portátiles vendidos en el mundo</p>
        </div>
        <div class="cifra">
          <p class="cifra__dato">34,2<span class="cifra__unidad">%</span></p>
          <p class="cifra__pie">Del mercado mundial de parlantes portátiles</p>
        </div>
        <div class="cifra">
          <p class="cifra__dato">5<span class="cifra__unidad">años</span></p>
          <p class="cifra__pie">Seguidos como marca líder de la categoría</p>
        </div>
        <div class="cifra">
          <p class="cifra__dato">1946</p>
          <p class="cifra__pie">Año en que James B. Lansing fundó la marca</p>
        </div>
      </div>
      <p class="cifras__fuente">
        Cifras publicadas por Harman International en septiembre de 2019, al
        anunciar los 100 millones de unidades en la feria IFA de Berlín.
      </p>
    </div>
  </section>

  <!-- ================= MARCA ================= -->
  <section class="seccion" id="marca">
    <div class="contenedor bloque-marca">
      <p class="etiqueta reveal">La marca</p>
      <p class="declaracion reveal">
        Desde <strong>1946</strong> construimos altavoces para los que no se
        conforman con escuchar. El mismo sonido que llena estadios y salas de
        cine cabe hoy en una mano.
      </p>
      <p class="firma reveal">James B. Lansing Sound</p>
    </div>
  </section>

  <!-- MARCA: galeria de parlantes -> feature/galeria-productos -->

  <!-- ================= TECNOLOGÍA ================= -->
  <section class="seccion tecnologia" id="tecnologia">
    <div class="contenedor duo">

      <figure class="duo__foto reveal">
        <img src="assets/img/tecnologia.jpg" alt="Vista frontal de un parlante JBL Charge 4 mostrando la rejilla y el logotipo" loading="lazy" width="1400" height="900">
      </figure>

      <div class="duo__texto">
        <p class="etiqueta reveal">La tecnología</p>
        <h2 class="titulo reveal">Lo que hay dentro</h2>

        <ul class="rasgos">
          <li class="rasgo reveal">
            <svg class="rasgo__ico" aria-hidden="true"><use href="#ico-sonido"/></svg>
            <div>
              <h3>JBL Pro Sound</h3>
              <p>La misma firma sonora de los estudios de grabación, calibrada para un equipo que cabe en una mochila.</p>
            </div>
          </li>
          <li class="rasgo reveal">
            <svg class="rasgo__ico" aria-hidden="true"><use href="#ico-agua"/></svg>
            <div>
              <h3>Resistencia al agua</h3>
              <p>Los modelos IP67 aguantan el polvo y hasta 30 minutos sumergidos a un metro.</p>
            </div>
          </li>
          <li class="rasgo reveal">
            <svg class="rasgo__ico" aria-hidden="true"><use href="#ico-bateria"/></svg>
            <div>
              <h3>Hasta 24 horas</h3>
              <p>Batería que cubre el viaje de ida, la fiesta y el regreso sin buscar un enchufe.</p>
            </div>
          </li>
          <li class="rasgo reveal">
            <svg class="rasgo__ico" aria-hidden="true"><use href="#ico-enlace"/></svg>
            <div>
              <h3>PartyBoost</h3>
              <p>Conecta varios parlantes compatibles y conviértelos en un solo sistema.</p>
            </div>
          </li>
        </ul>
      </div>

    </div>
  </section>

  <!-- ================= RESISTENCIA ================= -->
  <section class="seccion" id="resistencia">
    <div class="contenedor duo duo--invertido">

      <div class="duo__texto">
        <p class="etiqueta reveal">La resistencia</p>
        <h2 class="titulo reveal">Hecha para mojarse</h2>
        <p class="duo__parrafo reveal">
          La certificación no es un adorno: cada cifra del código IP describe
          una prueba concreta de laboratorio. Por eso un JBL se lleva a la
          playa sin pensarlo, se enjuaga bajo el grifo cuando vuelve lleno de
          arena y sigue sonando igual.
        </p>
        <p class="duo__parrafo reveal">
          La clave está en el sellado: los puertos de carga quedan bajo una
          tapa de goma y la rejilla es de tejido tratado, así que ni el polvo
          ni el agua llegan al altavoz.
        </p>

        <dl class="datos reveal">
          <div class="datos__fila">
            <dt>IP67</dt>
            <dd>Sellado total contra el polvo y hasta 30 minutos sumergido a un metro de profundidad.</dd>
          </div>
          <div class="datos__fila">
            <dt>IPX7</dt>
            <dd>Misma inmersión de 30 minutos a un metro, sin certificar la protección contra el polvo.</dd>
          </div>
          <div class="datos__fila">
            <dt>IPX5</dt>
            <dd>Aguanta chorros de agua desde cualquier ángulo: lluvia, salpicaduras y piscina.</dd>
          </div>
        </dl>
      </div>

      <figure class="duo__foto reveal">
        <img src="assets/img/resistencia.jpg" alt="Lateral de un JBL Go 2 con la tapa de goma que sella los puertos de carga" loading="lazy" width="1200" height="900">
      </figure>

    </div>
  </section>

  <!-- ================= PRACTICIDAD ================= -->
  <section class="seccion practicidad" id="practicidad">
    <div class="contenedor duo">

      <figure class="duo__foto reveal">
        <img src="assets/img/practicidad.jpg" alt="Un JBL Go 2 apoyado en la palma de una mano, más pequeño que los dedos" loading="lazy" width="1200" height="900">
      </figure>

      <div class="duo__texto">
        <p class="etiqueta reveal">La practicidad</p>
        <h2 class="titulo reveal">Pequeño,<br>pero se oye</h2>
        <p class="duo__parrafo reveal">
          El Go 2 cabe entero en la palma de la mano y pesa menos de 200
          gramos: cuelga de la correa de una mochila, entra en el bolsillo de
          un pantalón y no se nota hasta que suena.
        </p>
        <p class="duo__parrafo reveal">
          Esa es la idea que sostiene toda la línea. El mismo altavoz que te
          acompaña en el bus se convierte en un Boombox 3 de 180 vatios cuando
          la reunión crece, sin cambiar de marca ni de forma de usarlo.
        </p>

        <div class="comparativa reveal">
          <div class="comparativa__polo">
            <p class="comparativa__valor">3 W</p>
            <p class="comparativa__nombre">JBL Go 2</p>
            <p class="comparativa__nota">Cabe en una mano</p>
          </div>
          <div class="comparativa__barra" aria-hidden="true"></div>
          <div class="comparativa__polo comparativa__polo--fuerte">
            <p class="comparativa__valor">180 W</p>
            <p class="comparativa__nombre">JBL Boombox 3</p>
            <p class="comparativa__nota">Llena una azotea</p>
          </div>
        </div>
      </div>

    </div>
  </section>


  <!-- ================= CIERRE ================= -->
  <section class="cierre" id="contacto">
    <img class="cierre__fondo" src="assets/img/cierre.jpg" alt="" aria-hidden="true" loading="lazy">
    <div class="contenedor cierre__contenido">
      <h2 class="cierre__titulo reveal">El silencio<br>está sobrevalorado</h2>
      <p class="cierre__texto reveal">
        Encuentra el parlante que te acompaña en las tiendas autorizadas JBL
        de todo el país.
      </p>
      <a class="boton boton--solido boton--grande reveal" href="#parlantes">
        Explorar la línea completa
        <svg class="boton__ico" aria-hidden="true"><use href="#ico-flecha"/></svg>
      </a>
    </div>
  </section>

</main>

<footer class="pie">
  <div class="contenedor">

    <div class="pie__rejilla">

      <!-- Columna de marca -->
      <div class="pie__marca">
        <a class="marca" href="#inicio" aria-label="JBL, ir al inicio">
          <img src="assets/img/logo-jbl.svg" alt="JBL" width="88" height="62">
        </a>
        <p class="pie__resumen">
          James B. Lansing fundó la marca en 1946 fabricando altavoces para
          salas de cine. Hoy JBL es líder mundial en audio portátil, con más de
          100 millones de parlantes vendidos y presencia en más de 100 países.
        </p>
        <ul class="redes">
          <li>
            <a href="https://www.facebook.com/JBL" target="_blank" rel="noopener noreferrer" aria-label="JBL en Facebook">
              <svg class="redes__ico" aria-hidden="true"><use href="#red-facebook"/></svg>
            </a>
          </li>
          <li>
            <a href="https://www.instagram.com/jblaudio/" target="_blank" rel="noopener noreferrer" aria-label="JBL en Instagram">
              <svg class="redes__ico" aria-hidden="true"><use href="#red-instagram"/></svg>
            </a>
          </li>
          <li>
            <a href="https://x.com/JBLaudio" target="_blank" rel="noopener noreferrer" aria-label="JBL en X, antes Twitter">
              <svg class="redes__ico" aria-hidden="true"><use href="#red-x"/></svg>
            </a>
          </li>
          <li>
            <a href="https://www.youtube.com/@jbl" target="_blank" rel="noopener noreferrer" aria-label="JBL en YouTube">
              <svg class="redes__ico" aria-hidden="true"><use href="#red-youtube"/></svg>
            </a>
          </li>
        </ul>
      </div>

      <!-- Los parlantes -->
      <nav class="pie__columna" aria-label="Los parlantes">
        <h2 class="pie__titulo">Los parlantes</h2>
        <ul>
          <li><a href="#go-2">JBL Go 2</a></li>
          <li><a href="#flip-3">JBL Flip 3</a></li>
          <li><a href="#charge-4">JBL Charge 4</a></li>
          <li><a href="#xtreme">JBL Xtreme</a></li>
          <li><a href="#boombox-2">JBL Boombox 2</a></li>
          <li><a href="#boombox-3">JBL Boombox 3</a></li>
        </ul>
      </nav>

      <!-- Este sitio -->
      <nav class="pie__columna" aria-label="Secciones del sitio">
        <h2 class="pie__titulo">Este sitio</h2>
        <ul>
          <li><a href="#marca">La marca</a></li>
          <li><a href="#parlantes">Los parlantes</a></li>
          <li><a href="#tecnologia">La tecnología</a></li>
          <li><a href="#resistencia">La resistencia</a></li>
          <li><a href="#practicidad">La practicidad</a></li>
        </ul>
      </nav>

      <!-- Enlaces externos -->
      <nav class="pie__columna" aria-label="Enlaces oficiales">
        <h2 class="pie__titulo">Oficial</h2>
        <ul>
          <li>
            <a href="https://www.jbl.com/" target="_blank" rel="noopener noreferrer">
              Sitio global de JBL
              <svg class="pie__ico" aria-hidden="true"><use href="#ico-externo"/></svg>
            </a>
          </li>
          <li>
            <a href="https://www.jbl.com.pe/" target="_blank" rel="noopener noreferrer">
              JBL Perú
              <svg class="pie__ico" aria-hidden="true"><use href="#ico-externo"/></svg>
            </a>
          </li>
          <li>
            <a href="https://www.harman.com/" target="_blank" rel="noopener noreferrer">
              Harman International
              <svg class="pie__ico" aria-hidden="true"><use href="#ico-externo"/></svg>
            </a>
          </li>
          <li>
            <a href="docs/creditos-imagenes.md">Créditos de imágenes</a>
          </li>
        </ul>
      </nav>

    </div>

    <!-- Aviso legal -->
    <div class="pie__barra">
      <p class="pie__aviso">
        <strong>Proyecto académico sin fines comerciales.</strong> Este sitio no
        es oficial ni está afiliado a JBL. La marca JBL, su logotipo y los
        nombres de los productos son propiedad de Harman International
        Industries, Inc., y se reproducen aquí únicamente con fines educativos.
        Las fotografías proceden de Wikimedia Commons bajo licencias libres; su
        autoría se detalla en <code>docs/creditos-imagenes.md</code>.
      </p>
      <p class="pie__firma">
        Curso de Herramientas de Desarrollo · 2026
      </p>
    </div>

  </div>
</footer>

<script src="js/main.js"></script>
</body>
</html>
FIN_DE_ARCHIVO_JBL
git add css/estilos.css index.html
git commit -m 'feat: agrega tecnologia, resistencia, practicidad y el pie'
echo ""
echo "--> Correccion: los iconos se pintaban rellenos de negro"
cat > css/estilos.css <<'FIN_DE_ARCHIVO_JBL'
/* =========================================================================
   JBL — Landing publicitaria
   Fondo oscuro con un solo color de acento. Las fotografías mandan; el
   movimiento se limita a una aparición suave al hacer scroll.
   ========================================================================= */

/* ------------------------------ 1. TOKENS ------------------------------ */

:root {
  /* Color */
  --fondo:        #0a0a0b;
  --superficie:   #141417;
  --superficie-2: #1c1c21;
  --borde:        #26262c;
  --borde-fuerte: #3a3a43;

  --tinta:        #f5f5f3;
  --tinta-suave:  #a8a8b0;

  --acento:       #ff6a00;
  --acento-claro: #ff8c3d;

  /* Tipografía */
  --display: "Bebas Neue", "Arial Narrow", Impact, sans-serif;
  --texto:   "Source Sans 3", -apple-system, "Segoe UI", Roboto, sans-serif;

  /* Ritmo vertical: TODAS las secciones usan el mismo valor. De ahí viene
     la sensación de orden; mezclar separaciones es lo que descuadra. */
  --seccion:   clamp(4.5rem, 9vw, 7.5rem);
  --contenedor: 1200px;
  --margen:    clamp(1.25rem, 5vw, 3rem);

  /* Escala de espaciado */
  --e-xs: 0.35rem;
  --e-sm: 0.75rem;
  --e-md: 1.25rem;
  --e-lg: 2rem;
  --e-xl: 3rem;
  --e-2xl: 4.5rem;

  --radio: 6px;

  /* Movimiento */
  --curva: cubic-bezier(0.22, 1, 0.36, 1);
  --rapido: 180ms;
  --medio: 320ms;

  --z-fondo: 1;
  --z-contenido: 10;
  --z-nav: 30;

  color-scheme: dark;
}

/* ------------------------------ 2. BASE ------------------------------ */

*, *::before, *::after { box-sizing: border-box; }
* { margin: 0; padding: 0; }

html {
  scroll-behavior: smooth;
  scroll-padding-top: 5rem;
  -webkit-text-size-adjust: 100%;
}

body {
  background: var(--fondo);
  color: var(--tinta);
  font-family: var(--texto);
  font-size: 1.0625rem;
  line-height: 1.65;
  overflow-x: hidden;
  -webkit-font-smoothing: antialiased;
}

img { display: block; max-width: 100%; height: auto; }
svg { display: block; }
a { color: inherit; text-decoration: none; }
ul { list-style: none; }

.sprite { position: absolute; width: 0; height: 0; overflow: hidden; }

:focus-visible {
  outline: 2px solid var(--acento);
  outline-offset: 3px;
  border-radius: 2px;
}

.salto {
  position: absolute;
  left: var(--e-md);
  top: -100px;
  z-index: 100;
  padding: 0.75rem 1.25rem;
  background: var(--acento);
  color: #0a0a0b;
  font-weight: 700;
  transition: top var(--rapido) var(--curva);
}
.salto:focus { top: var(--e-md); }

/* ------------------------------ 3. ESTRUCTURA ------------------------------ */

/* Un único ancho de contenedor para toda la página: así los bordes de
   cada sección caen siempre en la misma línea vertical. */
.contenedor {
  width: 100%;
  max-width: var(--contenedor);
  margin-inline: auto;
  padding-inline: var(--margen);
}

.seccion { padding-block: var(--seccion); }

.cabecera {
  display: flex;
  flex-direction: column;
  gap: var(--e-sm);
  max-width: 46ch;
  margin-bottom: var(--e-2xl);
}
.cabecera__texto {
  color: var(--tinta-suave);
  text-wrap: pretty;
}

/* ------------------------------ 4. TIPOGRAFÍA ------------------------------ */

.etiqueta {
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  color: var(--acento);
}

.titulo {
  font-family: var(--display);
  font-size: clamp(2.75rem, 6.5vw, 5rem);
  line-height: 0.92;
  letter-spacing: 0.01em;
  text-transform: uppercase;
  text-wrap: balance;
}

.acento { color: var(--acento); }

/* ------------------------------ 5. BOTONES ------------------------------ */

.acciones {
  display: flex;
  flex-wrap: wrap;
  gap: var(--e-md);
}

.boton {
  display: inline-flex;
  align-items: center;
  gap: 0.6rem;
  padding: 0.95rem 1.7rem;
  font-size: 0.9375rem;
  font-weight: 700;
  letter-spacing: 0.02em;
  border-radius: var(--radio);
  border: 2px solid transparent;
  cursor: pointer;
  transition: background-color var(--rapido) var(--curva),
              border-color var(--rapido) var(--curva),
              color var(--rapido) var(--curva);
}

.boton--solido { background: var(--acento); color: #0a0a0b; }
.boton--solido:hover { background: var(--acento-claro); }

.boton--linea { border-color: rgba(245, 245, 243, 0.35); color: var(--tinta); }
.boton--linea:hover {
  border-color: var(--tinta);
  background: rgba(245, 245, 243, 0.08);
}

.boton--grande { padding: 1.15rem 2.2rem; font-size: 1rem; }

.boton__ico {
  width: 18px;
  height: 18px;
  fill: none;
  stroke: currentColor;
  stroke-width: 2.2;
  stroke-linecap: round;
  stroke-linejoin: round;
  transition: transform var(--rapido) var(--curva);
}
.boton:hover .boton__ico { transform: translateX(4px); }

/* ------------------------------ 6. APARICIÓN ------------------------------ */

/* Sólo se esconde si el navegador ejecuta JavaScript; sin él, la página
   se ve completa. */
.reveal {
  opacity: 0;
  transform: translateY(16px);
  transition: opacity var(--medio) var(--curva),
              transform var(--medio) var(--curva);
}
.reveal.visible { opacity: 1; transform: none; }

/* ------------------------------ 7. NAVEGACIÓN ------------------------------ */

.nav {
  position: fixed;
  inset: 0 0 auto 0;
  z-index: var(--z-nav);
  border-bottom: 1px solid transparent;
  transition: background-color var(--medio) var(--curva),
              border-color var(--medio) var(--curva);
}
.nav.compacta {
  background: rgba(10, 10, 11, 0.85);
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
  border-bottom-color: var(--borde);
}

.nav__caja {
  max-width: var(--contenedor);
  margin-inline: auto;
  padding: 1.1rem var(--margen);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: var(--e-lg);
}

/* El logotipo es el oficial, en SVG: escala sin perder nitidez. */
.marca { display: inline-flex; align-items: center; }
.marca img { width: 64px; height: auto; }

.nav__menu { display: flex; gap: var(--e-xl); font-size: 0.9375rem; font-weight: 600; }
.nav__menu a { color: var(--tinta-suave); transition: color var(--rapido) var(--curva); }
.nav__menu a:hover { color: var(--tinta); }

.nav__boton {
  display: none;
  flex-direction: column;
  justify-content: center;
  gap: 5px;
  width: 44px;
  height: 44px;
  background: none;
  border: 1px solid var(--borde-fuerte);
  border-radius: var(--radio);
  cursor: pointer;
}
.nav__boton span {
  display: block;
  width: 18px;
  height: 2px;
  margin-inline: auto;
  background: var(--tinta);
  transition: transform var(--rapido) var(--curva);
}
.nav__boton[aria-expanded="true"] span:first-child { transform: translateY(3.5px) rotate(45deg); }
.nav__boton[aria-expanded="true"] span:last-child  { transform: translateY(-3.5px) rotate(-45deg); }

/* ------------------------------ 8. PORTADA ------------------------------ */

.portada {
  position: relative;
  min-height: min(92svh, 820px);
  display: flex;
  align-items: flex-end;
  padding-block: calc(var(--seccion) + 4rem) var(--seccion);
  overflow: hidden;
}

/* La foto va detrás, a sangre, con un velo que garantiza el contraste. */
.portada__fondo {
  position: absolute;
  inset: 0;
  z-index: var(--z-fondo);
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
}
.portada::after {
  content: "";
  position: absolute;
  inset: 0;
  z-index: var(--z-fondo);
  background:
    linear-gradient(to right, rgba(10,10,11,.92) 0%, rgba(10,10,11,.62) 55%, rgba(10,10,11,.45) 100%),
    linear-gradient(to top, var(--fondo) 2%, transparent 45%);
}

.portada__contenido {
  position: relative;
  z-index: var(--z-contenido);
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: var(--e-md);
}

.portada__titulo {
  font-family: var(--display);
  font-size: clamp(3.25rem, 10vw, 8rem);
  line-height: 0.86;
  letter-spacing: 0.01em;
  text-transform: uppercase;
}

.portada__bajada {
  max-width: 46ch;
  font-size: 1.125rem;
  color: var(--tinta-suave);
  text-wrap: pretty;
}

/* ------------------------------ 9. CIFRAS ------------------------------ */

.cifras {
  border-block: 1px solid var(--borde);
  background: var(--superficie);
}
.cifras__rejilla {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: var(--e-lg);
  padding-block: var(--e-xl);
}
.cifra { text-align: center; }
.cifra__dato {
  font-family: var(--display);
  font-size: clamp(2.25rem, 4.5vw, 3.25rem);
  line-height: 1;
  letter-spacing: 0.02em;
  font-variant-numeric: tabular-nums;
  color: var(--acento);
}
/* La unidad va debajo y más pequeña: la cifra se lee de un golpe. */
.cifra__unidad {
  display: block;
  margin-top: 0.1rem;
  font-family: var(--texto);
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 0.16em;
  text-transform: uppercase;
  color: var(--tinta-suave);
}
.cifra__pie {
  margin-top: var(--e-sm);
  max-width: 22ch;
  margin-inline: auto;
  font-size: 0.8125rem;
  line-height: 1.45;
  color: var(--tinta-suave);
  text-wrap: pretty;
}
.cifras__fuente {
  margin-top: var(--e-lg);
  padding-top: var(--e-md);
  border-top: 1px solid var(--borde);
  font-size: 0.75rem;
  line-height: 1.5;
  color: var(--tinta-suave);
  text-align: center;
}

/* ------------------------------ 10. MARCA ------------------------------ */

.bloque-marca {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: var(--e-md);
  text-align: center;
}
.declaracion {
  max-width: 30ch;
  font-family: var(--display);
  font-size: clamp(2.25rem, 5.5vw, 4.25rem);
  line-height: 1.02;
  letter-spacing: 0.012em;
  text-transform: uppercase;
  text-wrap: balance;
}
.declaracion strong { color: var(--acento); font-weight: 400; }
.firma {
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  color: var(--tinta-suave);
}

/* MARCA: estilos de la galeria -> feature/galeria-productos */

/* ------------------------------ 12. TECNOLOGÍA ------------------------------ */

.tecnologia { background: var(--superficie); border-block: 1px solid var(--borde); }
.practicidad { background: var(--superficie); border-block: 1px solid var(--borde); }

/* Bloque de dos columnas reutilizable: foto a un lado, texto al otro.
   Las secciones alternan el lado con .duo--invertido. */
.duo {
  display: grid;
  grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
  gap: clamp(2rem, 5vw, 4rem);
  align-items: center;
}

.duo__foto {
  border-radius: var(--radio);
  overflow: hidden;
  border: 1px solid var(--borde);
}
.duo__foto img { width: 100%; aspect-ratio: 4 / 3.4; object-fit: cover; }

.duo__texto { display: flex; flex-direction: column; gap: var(--e-sm); }

@media (min-width: 1001px) {
  .duo--invertido .duo__texto { order: -1; }
}
.duo__parrafo {
  max-width: 52ch;
  color: var(--tinta-suave);
  text-wrap: pretty;
}
.duo__parrafo:first-of-type { margin-top: var(--e-xs); }

/* Lista de definiciones para los grados de protección */
.datos {
  margin-top: var(--e-md);
  border-top: 1px solid var(--borde);
}
.datos__fila {
  display: grid;
  grid-template-columns: 5.5rem minmax(0, 1fr);
  gap: var(--e-md);
  padding: var(--e-sm) 0;
  border-bottom: 1px solid var(--borde);
}
.datos__fila dt {
  font-family: var(--display);
  font-size: 1.35rem;
  line-height: 1.1;
  letter-spacing: 0.04em;
  color: var(--acento);
}
.datos__fila dd {
  font-size: 0.9375rem;
  color: var(--tinta-suave);
  text-wrap: pretty;
}

/* Comparación de los dos extremos de la línea */
.comparativa {
  display: grid;
  grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
  align-items: center;
  gap: var(--e-md);
  margin-top: var(--e-lg);
  padding: var(--e-md);
  border: 1px solid var(--borde);
  border-radius: var(--radio);
  background: var(--fondo);
}
.comparativa__polo { text-align: center; }
.comparativa__valor {
  font-family: var(--display);
  font-size: 2rem;
  line-height: 1;
  letter-spacing: 0.02em;
  font-variant-numeric: tabular-nums;
}
.comparativa__polo--fuerte .comparativa__valor { color: var(--acento); }
.comparativa__nombre {
  margin-top: 0.2rem;
  font-size: 0.8125rem;
  font-weight: 700;
}
.comparativa__nota {
  font-size: 0.75rem;
  color: var(--tinta-suave);
}
.comparativa__barra {
  width: clamp(2rem, 6vw, 4.5rem);
  height: 2px;
  border-radius: 2px;
  background: linear-gradient(to right, var(--borde-fuerte), var(--acento));
}

.rasgos {
  display: flex;
  flex-direction: column;
  gap: var(--e-md);
  margin-top: var(--e-md);
}
.rasgo {
  display: grid;
  grid-template-columns: auto minmax(0, 1fr);
  gap: var(--e-md);
  align-items: start;
  padding-top: var(--e-md);
  border-top: 1px solid var(--borde);
}
.rasgo:first-child { padding-top: 0; border-top: 0; }
.rasgo__ico {
  width: 26px;
  height: 26px;
  margin-top: 0.2rem;
  color: var(--acento);
  fill: none;
  stroke: currentColor;
  stroke-width: 1.7;
  stroke-linecap: round;
  stroke-linejoin: round;
}
.rasgo h3 {
  font-family: var(--display);
  font-size: 1.5rem;
  line-height: 1.1;
  letter-spacing: 0.02em;
  text-transform: uppercase;
  font-weight: 400;
}
.rasgo p {
  margin-top: 0.15rem;
  font-size: 0.9375rem;
  color: var(--tinta-suave);
  text-wrap: pretty;
}


/* ------------------------------ 13. CIERRE ------------------------------ */

.cierre {
  position: relative;
  padding-block: calc(var(--seccion) * 1.15);
  overflow: hidden;
}
.cierre__fondo {
  position: absolute;
  inset: 0;
  z-index: var(--z-fondo);
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.cierre::after {
  content: "";
  position: absolute;
  inset: 0;
  z-index: var(--z-fondo);
  background: linear-gradient(to right, rgba(10,10,11,.94) 10%, rgba(10,10,11,.55) 100%);
}
.cierre__contenido {
  position: relative;
  z-index: var(--z-contenido);
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: var(--e-md);
}
.cierre__titulo {
  font-family: var(--display);
  font-size: clamp(2.75rem, 8vw, 6.5rem);
  line-height: 0.88;
  letter-spacing: 0.01em;
  text-transform: uppercase;
}
.cierre__texto {
  max-width: 44ch;
  font-size: 1.125rem;
  color: var(--tinta-suave);
  text-wrap: pretty;
}

/* ------------------------------ 14. PIE ------------------------------ */

.pie {
  padding-block: var(--e-2xl) var(--e-xl);
  border-top: 1px solid var(--borde);
  background: var(--superficie);
}

/* La columna de marca pesa más que las de enlaces: por eso 1.6fr. */
.pie__rejilla {
  display: grid;
  grid-template-columns: 1.6fr repeat(3, 1fr);
  gap: clamp(2rem, 4vw, 3.5rem);
  padding-bottom: var(--e-xl);
  border-bottom: 1px solid var(--borde);
}

.pie__marca {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: var(--e-md);
}
.pie__marca .marca img { width: 88px; }

.pie__resumen {
  max-width: 42ch;
  font-size: 0.9375rem;
  line-height: 1.6;
  color: var(--tinta-suave);
  text-wrap: pretty;
}

/* Redes sociales */
.redes {
  display: flex;
  gap: var(--e-sm);
  margin-top: var(--e-xs);
}
.redes a {
  display: grid;
  place-items: center;
  width: 42px;
  height: 42px;
  border: 1px solid var(--borde);
  border-radius: var(--radio);
  color: var(--tinta-suave);
  transition: color var(--rapido) var(--curva),
              border-color var(--rapido) var(--curva),
              background-color var(--rapido) var(--curva);
}
.redes a:hover {
  color: #0a0a0b;
  background: var(--acento);
  border-color: var(--acento);
}
.redes__ico {
  width: 18px;
  height: 18px;
  fill: currentColor;
}

/* Columnas de enlaces */
.pie__columna { display: flex; flex-direction: column; gap: var(--e-sm); }
.pie__titulo {
  font-family: var(--display);
  font-size: 1.15rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  font-weight: 400;
  color: var(--tinta);
}
.pie__columna ul { display: flex; flex-direction: column; gap: 0.45rem; }
.pie__columna a {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.9375rem;
  color: var(--tinta-suave);
  transition: color var(--rapido) var(--curva);
}
.pie__columna a:hover { color: var(--acento); }
.pie__ico {
  width: 13px;
  height: 13px;
  fill: none;
  stroke: currentColor;
  stroke-width: 2;
  stroke-linecap: round;
  stroke-linejoin: round;
  opacity: 0.65;
}

/* Aviso legal */
.pie__barra {
  display: flex;
  flex-wrap: wrap;
  align-items: flex-start;
  justify-content: space-between;
  gap: var(--e-md);
  padding-top: var(--e-lg);
}
.pie__aviso {
  flex: 1 1 42ch;
  max-width: 82ch;
  font-size: 0.8125rem;
  line-height: 1.65;
  color: var(--tinta-suave);
  text-wrap: pretty;
}
.pie__aviso strong { color: var(--tinta); font-weight: 600; }
.pie__aviso code {
  font-family: ui-monospace, Consolas, monospace;
  font-size: 0.95em;
  color: var(--tinta);
}
.pie__firma {
  flex: 0 0 auto;
  font-size: 0.75rem;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--tinta-suave);
}


/* ------------------------------ 15. RESPONSIVE ------------------------------ */

@media (max-width: 1000px) {
  .galeria { grid-template-columns: repeat(2, 1fr); }
  .pie__rejilla { grid-template-columns: repeat(2, 1fr); }
  .pie__marca { grid-column: 1 / -1; }
  .cifras__rejilla { grid-template-columns: repeat(2, 1fr); row-gap: var(--e-xl); }

  /* En una sola columna la foto siempre va arriba, sin importar el orden
     que tuviera en el escritorio. */
  .duo { grid-template-columns: minmax(0, 1fr); }
  .duo__foto { order: -1; }
}

@media (max-width: 860px) {
  .nav__boton { display: flex; }

  .nav__menu {
    position: absolute;
    inset: 100% var(--margen) auto var(--margen);
    flex-direction: column;
    gap: 0;
    padding: var(--e-xs) 0;
    background: var(--superficie);
    border: 1px solid var(--borde);
    border-radius: var(--radio);
    opacity: 0;
    transform: translateY(-8px);
    transform-origin: top center;
    pointer-events: none;
    transition: opacity var(--rapido) var(--curva),
                transform var(--rapido) var(--curva);
  }
  .nav__menu.abierto { opacity: 1; transform: none; pointer-events: auto; }
  .nav__menu a { padding: 0.8rem var(--e-md); }

  .portada { min-height: auto; padding-block: calc(var(--seccion) + 3rem) var(--seccion); }
  .portada::after {
    background:
      linear-gradient(to right, rgba(10,10,11,.9) 0%, rgba(10,10,11,.72) 100%),
      linear-gradient(to top, var(--fondo) 2%, transparent 50%);
  }
}

@media (max-width: 560px) {
  .galeria { grid-template-columns: minmax(0, 1fr); }
  .pie__rejilla { grid-template-columns: minmax(0, 1fr); }
  .pie__marca { grid-column: auto; }
  .cifras__rejilla { grid-template-columns: minmax(0, 1fr); }
  .comparativa { grid-template-columns: minmax(0, 1fr); }
  .comparativa__barra { width: 100%; }
  .datos__fila { grid-template-columns: minmax(0, 1fr); gap: 0.2rem; }
  .boton { width: 100%; justify-content: center; }
  .acciones { width: 100%; }
  .declaracion { max-width: 100%; }
}

/* ------------------------------ 16. MOVIMIENTO REDUCIDO ------------------------------ */

@media (prefers-reduced-motion: reduce) {
  html { scroll-behavior: auto; }
  *, *::before, *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
  .reveal { opacity: 1; transform: none; }
  .tarjeta:hover .tarjeta__foto img { transform: none; }
}
FIN_DE_ARCHIVO_JBL
git add css/estilos.css
git commit -m 'style: corrige los iconos para que se dibujen a trazo'
echo ""

# ---- Publicar --------------------------------------------------------------
echo ""
echo "Subiendo a GitHub..."
echo "(si se abre el navegador, inicia sesion con TU cuenta de GitHub)"
echo ""
git push -u origin "$RAMA"

echo ""
echo "=================================================================="
git log --oneline -2 --decorate
echo "=================================================================="
echo ""
echo "  Listo. Tus 2 commits ya estan en $RAMA."
echo ""
