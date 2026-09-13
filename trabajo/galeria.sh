#!/usr/bin/env bash
# ==========================================================================
#  PROYECTO JBL  -  aporte de FABRIZIO
#  Rama: feature/galeria-productos
#
#  Ejecutalo desde la carpeta del repositorio:
#      bash trabajo/galeria.sh
# ==========================================================================
set -e

RAMA="feature/galeria-productos"

[ -d .git ] || {
  echo "ERROR: ejecuta esto dentro de la carpeta jbl-web."
  exit 1
}

echo ""
echo "=================================================================="
echo "   Aporte de FABRIZIO  ->  $RAMA"
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
echo "--> Galeria de los seis parlantes con sus fotografias"
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

/* ------------------------------ 11. PARLANTES ------------------------------ */

/* Rejilla uniforme: todas las tarjetas del mismo tamaño y la misma
   proporción de foto. Es lo que hace que el bloque se lea ordenado. */
.galeria {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: var(--e-lg);
}

.tarjeta {
  position: relative;
  display: flex;
  flex-direction: column;
  background: var(--superficie);
  border: 1px solid var(--borde);
  border-radius: var(--radio);
  overflow: hidden;
  transition: border-color var(--medio) var(--curva),
              background-color var(--medio) var(--curva);
}
.tarjeta:hover { border-color: var(--borde-fuerte); background: var(--superficie-2); }
.tarjeta--destacada { border-color: rgba(255, 106, 0, 0.55); }

.tarjeta__foto {
  aspect-ratio: 4 / 3;
  overflow: hidden;
  background: #000;
}
.tarjeta__foto img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 500ms var(--curva);
}
.tarjeta:hover .tarjeta__foto img { transform: scale(1.04); }

.tarjeta__sello {
  position: absolute;
  top: var(--e-sm);
  left: var(--e-sm);
  z-index: var(--z-contenido);
  padding: 0.3rem 0.7rem;
  border-radius: 999px;
  background: var(--acento);
  color: #0a0a0b;
  font-size: 0.6875rem;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
}

.tarjeta__cuerpo {
  display: flex;
  flex-direction: column;
  gap: var(--e-xs);
  padding: var(--e-md);
}
.tarjeta__tipo {
  font-size: 0.6875rem;
  font-weight: 600;
  letter-spacing: 0.16em;
  text-transform: uppercase;
  color: var(--acento);
}
.tarjeta__nombre {
  font-family: var(--display);
  font-size: 1.9rem;
  line-height: 1;
  letter-spacing: 0.02em;
  text-transform: uppercase;
  font-weight: 400;
}

.specs {
  margin-top: var(--e-sm);
  border-top: 1px solid var(--borde);
}
.specs li {
  display: flex;
  align-items: baseline;
  justify-content: space-between;
  gap: var(--e-md);
  padding: 0.5rem 0;
  border-bottom: 1px solid var(--borde);
  font-size: 0.875rem;
}
.specs span { color: var(--tinta-suave); }
.specs strong { font-weight: 700; font-variant-numeric: tabular-nums; }


/* MARCA: estilos de tecnologia y resistencia -> feature/tecnologia-y-pie */

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

/* MARCA: estilos del pie -> feature/tecnologia-y-pie */

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
  <!-- MARCA: iconos de tecnologia -> feature/tecnologia-y-pie -->
  <symbol id="ico-flecha" viewBox="0 0 24 24"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></symbol>
  <symbol id="ico-externo" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><path d="M15 3h6v6"/><path d="M10 14 21 3"/></symbol>
</svg>

<!-- MARCA: logotipos de redes -> feature/tecnologia-y-pie -->

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

  <!-- ================= PARLANTES ================= -->
  <section class="seccion" id="parlantes">
    <div class="contenedor">
      <header class="cabecera">
        <p class="etiqueta reveal">Los parlantes</p>
        <h2 class="titulo reveal">Elige tu volumen</h2>
        <p class="cabecera__texto reveal">
          Seis modelos, del que cabe en el bolsillo al que llena una azotea.
          Todos con JBL Pro&nbsp;Sound.
        </p>
      </header>

      <div class="galeria">

        <article id="go-2" class="tarjeta reveal">
          <div class="tarjeta__foto">
            <img src="assets/img/productos/go-2.jpg" alt="Parlante JBL Go 2 azul sostenido en la palma de una mano" loading="lazy" width="900" height="675">
          </div>
          <div class="tarjeta__cuerpo">
            <p class="tarjeta__tipo">Ultra compacto</p>
            <h3 class="tarjeta__nombre">JBL Go 2</h3>
            <ul class="specs">
              <li><span>Potencia</span><strong>3 W</strong></li>
              <li><span>Batería</span><strong>5 h</strong></li>
              <li><span>Resistencia</span><strong>IPX7</strong></li>
            </ul>
          </div>
        </article>

        <article id="flip-3" class="tarjeta reveal">
          <div class="tarjeta__foto">
            <img src="assets/img/productos/flip-3.jpg" alt="Parlante JBL Flip 3 turquesa apoyado sobre una mesa de madera" loading="lazy" width="900" height="675">
          </div>
          <div class="tarjeta__cuerpo">
            <p class="tarjeta__tipo">Portátil</p>
            <h3 class="tarjeta__nombre">JBL Flip 3</h3>
            <ul class="specs">
              <li><span>Potencia</span><strong>160 W</strong></li>
              <li><span>Batería</span><strong>10 h</strong></li>
              <li><span>Resistencia</span><strong>IPX5</strong></li>
            </ul>
          </div>
        </article>

        <article id="charge-4" class="tarjeta reveal">
          <div class="tarjeta__foto">
            <img src="assets/img/productos/charge-4.jpg" alt="Parlante JBL Charge 4 negro sobre una superficie naranja" loading="lazy" width="900" height="675">
          </div>
          <div class="tarjeta__cuerpo">
            <p class="tarjeta__tipo">Portátil · Powerbank</p>
            <h3 class="tarjeta__nombre">JBL Charge 4</h3>
            <ul class="specs">
              <li><span>Potencia</span><strong>30 W</strong></li>
              <li><span>Batería</span><strong>2 h</strong></li>
              <li><span>Resistencia</span><strong>IPX7</strong></li>
            </ul>
          </div>
        </article>

        <article id="xtreme" class="tarjeta reveal">
          <div class="tarjeta__foto">
            <img src="assets/img/productos/xtreme.jpg" alt="Detalle del radiador pasivo lateral de un parlante JBL Xtreme" loading="lazy" width="900" height="675">
          </div>
          <div class="tarjeta__cuerpo">
            <p class="tarjeta__tipo">Portátil con correa</p>
            <h3 class="tarjeta__nombre">JBL Xtreme</h3>
            <ul class="specs">
              <li><span>Potencia</span><strong>40 W</strong></li>
              <li><span>Batería</span><strong>15 h</strong></li>
              <li><span>Resistencia</span><strong>IPX5</strong></li>
            </ul>
          </div>
        </article>

        <article id="boombox-2" class="tarjeta reveal">
          <div class="tarjeta__foto">
            <img src="assets/img/productos/boombox-2.jpg" alt="Parlante JBL Boombox 2 con acabado de camuflaje y asa de transporte" loading="lazy" width="900" height="675">
          </div>
          <div class="tarjeta__cuerpo">
            <p class="tarjeta__tipo">Fiesta</p>
            <h3 class="tarjeta__nombre">JBL Boombox 2</h3>
            <ul class="specs">
              <li><span>Potencia</span><strong>80 W</strong></li>
              <li><span>Batería</span><strong>24 h</strong></li>
              <li><span>Resistencia</span><strong>IPX7</strong></li>
            </ul>
          </div>
        </article>

        <article id="boombox-3" class="tarjeta tarjeta--destacada reveal">
          <p class="tarjeta__sello">El más potente</p>
          <div class="tarjeta__foto">
            <img src="assets/img/productos/boombox-3.jpg" alt="Parlante JBL Boombox 3 negro sujeto por su asa" loading="lazy" width="900" height="675">
          </div>
          <div class="tarjeta__cuerpo">
            <p class="tarjeta__tipo">Fiesta</p>
            <h3 class="tarjeta__nombre">JBL Boombox 3</h3>
            <ul class="specs">
              <li><span>Potencia</span><strong>180 W</strong></li>
              <li><span>Batería</span><strong>24 h</strong></li>
              <li><span>Resistencia</span><strong>IP67</strong></li>
            </ul>
          </div>
        </article>

      </div>
    </div>
  </section>


  <!-- MARCA: tecnologia, resistencia y practicidad -> feature/tecnologia-y-pie -->

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

<!-- MARCA: pie de pagina -> feature/tecnologia-y-pie -->

<script src="js/main.js"></script>
</body>
</html>
FIN_DE_ARCHIVO_JBL
git add css/estilos.css index.html
git commit -m 'feat: agrega la galeria de parlantes con sus fichas tecnicas'
echo ""
echo "--> Correccion: dos fichas tenian datos equivocados"
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
  <!-- MARCA: iconos de tecnologia -> feature/tecnologia-y-pie -->
  <symbol id="ico-flecha" viewBox="0 0 24 24"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></symbol>
  <symbol id="ico-externo" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><path d="M15 3h6v6"/><path d="M10 14 21 3"/></symbol>
</svg>

<!-- MARCA: logotipos de redes -> feature/tecnologia-y-pie -->

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

  <!-- ================= PARLANTES ================= -->
  <section class="seccion" id="parlantes">
    <div class="contenedor">
      <header class="cabecera">
        <p class="etiqueta reveal">Los parlantes</p>
        <h2 class="titulo reveal">Elige tu volumen</h2>
        <p class="cabecera__texto reveal">
          Seis modelos, del que cabe en el bolsillo al que llena una azotea.
          Todos con JBL Pro&nbsp;Sound.
        </p>
      </header>

      <div class="galeria">

        <article id="go-2" class="tarjeta reveal">
          <div class="tarjeta__foto">
            <img src="assets/img/productos/go-2.jpg" alt="Parlante JBL Go 2 azul sostenido en la palma de una mano" loading="lazy" width="900" height="675">
          </div>
          <div class="tarjeta__cuerpo">
            <p class="tarjeta__tipo">Ultra compacto</p>
            <h3 class="tarjeta__nombre">JBL Go 2</h3>
            <ul class="specs">
              <li><span>Potencia</span><strong>3 W</strong></li>
              <li><span>Batería</span><strong>5 h</strong></li>
              <li><span>Resistencia</span><strong>IPX7</strong></li>
            </ul>
          </div>
        </article>

        <article id="flip-3" class="tarjeta reveal">
          <div class="tarjeta__foto">
            <img src="assets/img/productos/flip-3.jpg" alt="Parlante JBL Flip 3 turquesa apoyado sobre una mesa de madera" loading="lazy" width="900" height="675">
          </div>
          <div class="tarjeta__cuerpo">
            <p class="tarjeta__tipo">Portátil</p>
            <h3 class="tarjeta__nombre">JBL Flip 3</h3>
            <ul class="specs">
              <li><span>Potencia</span><strong>16 W</strong></li>
              <li><span>Batería</span><strong>10 h</strong></li>
              <li><span>Resistencia</span><strong>IPX5</strong></li>
            </ul>
          </div>
        </article>

        <article id="charge-4" class="tarjeta reveal">
          <div class="tarjeta__foto">
            <img src="assets/img/productos/charge-4.jpg" alt="Parlante JBL Charge 4 negro sobre una superficie naranja" loading="lazy" width="900" height="675">
          </div>
          <div class="tarjeta__cuerpo">
            <p class="tarjeta__tipo">Portátil · Powerbank</p>
            <h3 class="tarjeta__nombre">JBL Charge 4</h3>
            <ul class="specs">
              <li><span>Potencia</span><strong>30 W</strong></li>
              <li><span>Batería</span><strong>20 h</strong></li>
              <li><span>Resistencia</span><strong>IPX7</strong></li>
            </ul>
          </div>
        </article>

        <article id="xtreme" class="tarjeta reveal">
          <div class="tarjeta__foto">
            <img src="assets/img/productos/xtreme.jpg" alt="Detalle del radiador pasivo lateral de un parlante JBL Xtreme" loading="lazy" width="900" height="675">
          </div>
          <div class="tarjeta__cuerpo">
            <p class="tarjeta__tipo">Portátil con correa</p>
            <h3 class="tarjeta__nombre">JBL Xtreme</h3>
            <ul class="specs">
              <li><span>Potencia</span><strong>40 W</strong></li>
              <li><span>Batería</span><strong>15 h</strong></li>
              <li><span>Resistencia</span><strong>IPX5</strong></li>
            </ul>
          </div>
        </article>

        <article id="boombox-2" class="tarjeta reveal">
          <div class="tarjeta__foto">
            <img src="assets/img/productos/boombox-2.jpg" alt="Parlante JBL Boombox 2 con acabado de camuflaje y asa de transporte" loading="lazy" width="900" height="675">
          </div>
          <div class="tarjeta__cuerpo">
            <p class="tarjeta__tipo">Fiesta</p>
            <h3 class="tarjeta__nombre">JBL Boombox 2</h3>
            <ul class="specs">
              <li><span>Potencia</span><strong>80 W</strong></li>
              <li><span>Batería</span><strong>24 h</strong></li>
              <li><span>Resistencia</span><strong>IPX7</strong></li>
            </ul>
          </div>
        </article>

        <article id="boombox-3" class="tarjeta tarjeta--destacada reveal">
          <p class="tarjeta__sello">El más potente</p>
          <div class="tarjeta__foto">
            <img src="assets/img/productos/boombox-3.jpg" alt="Parlante JBL Boombox 3 negro sujeto por su asa" loading="lazy" width="900" height="675">
          </div>
          <div class="tarjeta__cuerpo">
            <p class="tarjeta__tipo">Fiesta</p>
            <h3 class="tarjeta__nombre">JBL Boombox 3</h3>
            <ul class="specs">
              <li><span>Potencia</span><strong>180 W</strong></li>
              <li><span>Batería</span><strong>24 h</strong></li>
              <li><span>Resistencia</span><strong>IP67</strong></li>
            </ul>
          </div>
        </article>

      </div>
    </div>
  </section>


  <!-- MARCA: tecnologia, resistencia y practicidad -> feature/tecnologia-y-pie -->

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

<!-- MARCA: pie de pagina -> feature/tecnologia-y-pie -->

<script src="js/main.js"></script>
</body>
</html>
FIN_DE_ARCHIVO_JBL
git add index.html
git commit -m 'fix: corrige la potencia del Flip 3 y la bateria del Charge 4'
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
