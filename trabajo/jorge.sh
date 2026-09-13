#!/usr/bin/env bash
# ==========================================================================
#  PROYECTO JBL  -  aporte de JORGE
#  Rama: feature/interactividad
#
#  Ejecutalo desde la carpeta del repositorio:
#      bash trabajo/jorge.sh
# ==========================================================================
set -e

RAMA="feature/interactividad"

[ -d .git ] || {
  echo "ERROR: ejecuta esto dentro de la carpeta jbl-web."
  exit 1
}

echo ""
echo "=================================================================="
echo "   Aporte de JORGE  ->  $RAMA"
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
echo "--> Menu movil, animaciones de entrada y contadores"
cat > js/main.js <<'FIN_DE_ARCHIVO_JBL'
/* =========================================================================
   JBL — Landing publicitaria
   Interacciones de la página. Todo anima solo transform y opacity,
   que son las propiedades que el navegador puede mover sin recalcular
   el layout (por eso no se siente lento).
   ========================================================================= */

document.addEventListener('DOMContentLoaded', () => {

  // Si el usuario pidió menos animaciones en su sistema, lo respetamos.
  const sinMovimiento = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------------------------------------------------------------------
     1. Barra de navegación: se vuelve sólida al bajar
     --------------------------------------------------------------------- */
  const nav = document.getElementById('nav');

  const actualizarNav = () => {
    nav.classList.toggle('compacta', window.scrollY > 40);
  };

  actualizarNav();
  window.addEventListener('scroll', actualizarNav, { passive: true });

  /* ---------------------------------------------------------------------
     2. Menú móvil
     --------------------------------------------------------------------- */
  const boton = document.getElementById('navBoton');
  const menu = document.getElementById('menu');

  const cerrarMenu = () => {
    menu.classList.remove('abierto');
    boton.setAttribute('aria-expanded', 'false');
    boton.setAttribute('aria-label', 'Abrir menú');
  };

  boton.addEventListener('click', () => {
    const abierto = menu.classList.toggle('abierto');
    boton.setAttribute('aria-expanded', String(abierto));
    boton.setAttribute('aria-label', abierto ? 'Cerrar menú' : 'Abrir menú');
  });

  // Al elegir una sección, el menú se cierra solo.
  menu.querySelectorAll('a').forEach((enlace) => {
    enlace.addEventListener('click', cerrarMenu);
  });

  // Escape también lo cierra (accesibilidad de teclado).
  document.addEventListener('keydown', (evento) => {
    if (evento.key === 'Escape') cerrarMenu();
  });

  /* ---------------------------------------------------------------------
     3. Aparición de los bloques al hacer scroll
     Se usa IntersectionObserver: el navegador avisa cuando un elemento
     entra en pantalla, sin tener que escuchar el scroll todo el tiempo.
     --------------------------------------------------------------------- */
  const elementos = document.querySelectorAll('.reveal');

  if (sinMovimiento) {
    elementos.forEach((el) => el.classList.add('visible'));
  } else {
    const observador = new IntersectionObserver((entradas) => {
      entradas.forEach((entrada, indice) => {
        if (!entrada.isIntersecting) return;

        // Retraso escalonado: los elementos entran en cascada, no de golpe.
        entrada.target.style.transitionDelay = `${indice * 70}ms`;
        entrada.target.classList.add('visible');

        observador.unobserve(entrada.target); // se anima una sola vez
      });
    }, {
      threshold: 0.15,
      rootMargin: '0px 0px -80px 0px'
    });

    elementos.forEach((el) => observador.observe(el));
  }

  /* ---------------------------------------------------------------------
     4. Contadores del hero (180 W, 24 h)
     --------------------------------------------------------------------- */
  const contadores = document.querySelectorAll('[data-contador]');

  const animarContador = (elemento) => {
    const destino = Number(elemento.dataset.contador);
    const duracion = 1100;
    const inicio = performance.now();

    const paso = (ahora) => {
      const avance = Math.min((ahora - inicio) / duracion, 1);
      // Curva de salida: rápido al principio, suave al final.
      const suavizado = 1 - Math.pow(1 - avance, 3);

      elemento.textContent = Math.round(destino * suavizado);

      if (avance < 1) requestAnimationFrame(paso);
    };

    requestAnimationFrame(paso);
  };

  if (sinMovimiento) {
    contadores.forEach((el) => { el.textContent = el.dataset.contador; });
  } else {
    const observadorNumeros = new IntersectionObserver((entradas) => {
      entradas.forEach((entrada) => {
        if (!entrada.isIntersecting) return;
        animarContador(entrada.target);
        observadorNumeros.unobserve(entrada.target);
      });
    }, { threshold: 0.5 });

    contadores.forEach((el) => observadorNumeros.observe(el));
  }

});
FIN_DE_ARCHIVO_JBL
git add js/main.js
git commit -m 'feat: agrega el menu movil, las animaciones y los contadores'
echo ""
echo "--> Correccion: la pagina se veia en negro sin JavaScript"
cat > css/estilos.css <<'FIN_DE_ARCHIVO_JBL'
/* =========================================================================
   JBL — Landing publicitaria
   Sistema de diseño: dark audio + un solo acento (naranja de marca).
   Estilo: minimalismo exagerado (tipografía enorme, mucho aire).
   Generado con ui-ux-pro-max; ver docs/design-system/jbl-landing/
   ========================================================================= */

/* ------------------------------ 1. TOKENS ------------------------------ */

:root {
  /* Color */
  --fondo:        #0a0a0b;
  --superficie:   #121215;
  --superficie-2: #1a1a1e;
  --borde:        #26262c;
  --borde-fuerte: #34343c;

  --tinta:        #f5f5f3;
  --tinta-suave:  #9a9aa2;

  --acento:       #ff6a00;   /* naranja JBL */
  --acento-claro: #ff8c3d;

  /* Tipografía */
  --display: "Bebas Neue", "Arial Narrow", Impact, sans-serif;
  --texto:   "Source Sans 3", -apple-system, "Segoe UI", Roboto, sans-serif;

  /* Espaciado */
  --e-xs: 0.25rem;
  --e-sm: 0.5rem;
  --e-md: 1rem;
  --e-lg: 1.5rem;
  --e-xl: 2rem;
  --e-2xl: 3rem;
  --e-3xl: 4rem;
  --e-4xl: clamp(4rem, 12vw, 9rem);

  /* Estructura */
  --ancho: 1280px;
  --margen: clamp(1.25rem, 5vw, 4rem);
  --radio: 4px;

  /* Movimiento: curva propia, nunca el "ease" por defecto */
  --curva: cubic-bezier(0.22, 1, 0.36, 1);
  --rapido: 180ms;
  --medio: 300ms;

  /* Capas */
  --z-fondo: 10;
  --z-contenido: 20;
  --z-nav: 30;

  color-scheme: dark;
}

/* ------------------------------ 2. BASE ------------------------------ */

*, *::before, *::after { box-sizing: border-box; }

* { margin: 0; padding: 0; }

html {
  scroll-behavior: smooth;
  -webkit-text-size-adjust: 100%;
}

body {
  background: var(--fondo);
  color: var(--tinta);
  font-family: var(--texto);
  font-size: 1.0625rem;
  line-height: 1.65;
  font-weight: 400;
  overflow-x: hidden;
  -webkit-font-smoothing: antialiased;
}

img, svg { display: block; max-width: 100%; }

a { color: inherit; text-decoration: none; }

/* El sprite guarda los símbolos SVG; no debe ocupar espacio ni verse. */
.sprite {
  position: absolute;
  width: 0;
  height: 0;
  overflow: hidden;
}

/* Foco visible y consistente en todo lo navegable por teclado */
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

/* ------------------------------ 3. PIEZAS COMUNES ------------------------------ */

.etiqueta {
  font-family: var(--texto);
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  color: var(--tinta-suave);
}

.titulo {
  font-family: var(--display);
  font-size: clamp(3rem, 9vw, 7.5rem);
  line-height: 0.88;
  letter-spacing: 0.01em;
  text-transform: uppercase;
  text-wrap: balance;
}

.seccion__cabeza {
  display: flex;
  flex-direction: column;
  gap: var(--e-md);
  margin-bottom: var(--e-3xl);
}

/* Botones */
.boton {
  display: inline-flex;
  align-items: center;
  gap: 0.6rem;
  padding: 0.95rem 1.6rem;
  font-family: var(--texto);
  font-size: 0.9375rem;
  font-weight: 700;
  letter-spacing: 0.02em;
  border-radius: var(--radio);
  border: 2px solid transparent;
  cursor: pointer;
  transition: background-color var(--rapido) var(--curva),
              color var(--rapido) var(--curva),
              border-color var(--rapido) var(--curva);
}

.boton--solido {
  background: var(--acento);
  color: #0a0a0b;
}
.boton--solido:hover { background: var(--acento-claro); }

.boton--linea {
  border-color: var(--borde-fuerte);
  color: var(--tinta);
}
.boton--linea:hover {
  border-color: var(--tinta);
  background: rgba(245, 245, 243, 0.06);
}

.boton--grande {
  padding: 1.15rem 2.2rem;
  font-size: 1rem;
}

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

/* Siluetas de producto */
.figura {
  width: 100%;
  aspect-ratio: 2 / 1;
  height: auto;
}

/* Aparición al hacer scroll (la clase .visible la pone main.js).
   Ojo: sólo se esconde si el <html> tiene la clase .js, es decir, si el
   navegador ejecutó JavaScript. Sin JS la página se ve completa. */
.js .reveal {
  opacity: 0;
  transform: translateY(20px);
  transition: opacity var(--medio) var(--curva),
              transform var(--medio) var(--curva);
}
.js .reveal.visible {
  opacity: 1;
  transform: none;
}

/* ------------------------------ 4. NAVEGACIÓN ------------------------------ */

.nav {
  position: fixed;
  inset: 0 0 auto 0;
  z-index: var(--z-nav);
  transition: background-color var(--medio) var(--curva),
              border-color var(--medio) var(--curva);
  border-bottom: 1px solid transparent;
}

.nav.compacta {
  background: rgba(10, 10, 11, 0.82);
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
  border-bottom-color: var(--borde);
}

.nav__caja {
  max-width: var(--ancho);
  margin-inline: auto;
  padding: 1.1rem var(--margen);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: var(--e-lg);
}

.marca {
  display: inline-flex;
  align-items: center;
  gap: 0.55rem;
}
.marca__sello {
  width: 12px;
  height: 12px;
  background: var(--acento);
  border-radius: 2px;
}
.marca__texto {
  font-family: var(--display);
  font-size: 1.6rem;
  letter-spacing: 0.06em;
  line-height: 1;
}

.nav__menu {
  display: flex;
  gap: var(--e-xl);
  font-size: 0.9375rem;
  font-weight: 600;
}
.nav__menu a {
  color: var(--tinta-suave);
  transition: color var(--rapido) var(--curva);
}
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
  transition: transform var(--rapido) var(--curva),
              opacity var(--rapido) var(--curva);
}
.nav__boton[aria-expanded="true"] span:first-child { transform: translateY(3.5px) rotate(45deg); }
.nav__boton[aria-expanded="true"] span:last-child  { transform: translateY(-3.5px) rotate(-45deg); }

/* ------------------------------ 5. HERO ------------------------------ */

.hero {
  position: relative;
  min-height: 100svh;
  max-width: var(--ancho);
  margin-inline: auto;
  padding: clamp(7rem, 14vh, 10rem) var(--margen) var(--e-2xl);
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  gap: var(--e-3xl);
}

/* Asimetría deliberada: el texto pesa más que la imagen */
.hero__rejilla {
  flex: 1;
  display: grid;
  grid-template-columns: minmax(0, 1.15fr) minmax(0, 0.85fr);
  align-items: center;
  gap: var(--e-2xl);
}

.hero__texto {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: var(--e-lg);
}

.titular {
  font-family: var(--display);
  font-size: clamp(3.75rem, 12.5vw, 11rem);
  line-height: 0.82;
  letter-spacing: 0.01em;
  text-transform: uppercase;
}
/* La segunda línea entra desplazada: rompe el bloque centrado de siempre */
.titular__acento {
  display: block;
  color: var(--acento);
  margin-left: clamp(0rem, 4vw, 3.5rem);
}

.hero__bajada {
  max-width: 46ch;
  color: var(--tinta-suave);
  font-size: 1.125rem;
  text-wrap: pretty;
}

.hero__acciones {
  display: flex;
  flex-wrap: wrap;
  gap: var(--e-md);
  margin-top: var(--e-sm);
}

.hero__visual {
  position: relative;
  display: grid;
  place-items: center;
}

.figura--hero {
  position: relative;
  z-index: var(--z-contenido);
  filter: drop-shadow(0 30px 60px rgba(0, 0, 0, 0.8));
}

/* Ondas de sonido: sólo transform y opacity, nada que dispare layout */
.ondas {
  position: absolute;
  inset: 0;
  z-index: var(--z-fondo);
  display: grid;
  place-items: center;
  pointer-events: none;
}
.ondas span {
  position: absolute;
  width: min(26rem, 80%);
  aspect-ratio: 1;
  border: 1px solid var(--acento);
  border-radius: 50%;
  opacity: 0;
  animation: pulso 4s var(--curva) infinite;
}
.ondas span:nth-child(2) { animation-delay: 1.33s; }
.ondas span:nth-child(3) { animation-delay: 2.66s; }

@keyframes pulso {
  0%   { transform: scale(0.55); opacity: 0; }
  25%  { opacity: 0.5; }
  100% { transform: scale(1.35); opacity: 0; }
}

.hero__pie {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: var(--e-lg);
  padding-top: var(--e-lg);
  border-top: 1px solid var(--borde);
}

.datos {
  display: flex;
  flex-wrap: wrap;
  gap: clamp(1.5rem, 5vw, 4rem);
}
.datos__item dt {
  font-size: 0.6875rem;
  font-weight: 600;
  letter-spacing: 0.16em;
  text-transform: uppercase;
  color: var(--tinta-suave);
  margin-bottom: 0.15rem;
}
.datos__item dd {
  font-family: var(--display);
  font-size: 2rem;
  line-height: 1;
  letter-spacing: 0.02em;
  font-variant-numeric: tabular-nums;
}

.hero__scroll {
  display: grid;
  place-items: center;
  width: 46px;
  height: 46px;
  flex-shrink: 0;
  border: 1px solid var(--borde-fuerte);
  border-radius: 50%;
  color: var(--tinta-suave);
  transition: color var(--rapido) var(--curva),
              border-color var(--rapido) var(--curva);
  animation: flota 2.4s var(--curva) infinite;
}
.hero__scroll:hover { color: var(--acento); border-color: var(--acento); }
.hero__scroll svg {
  width: 18px;
  height: 18px;
  fill: none;
  stroke: currentColor;
  stroke-width: 2;
  stroke-linecap: round;
  stroke-linejoin: round;
}
@keyframes flota {
  0%, 100% { transform: translateY(0); }
  50%      { transform: translateY(5px); }
}

/* ------------------------------ 6. CINTA ------------------------------ */

.cinta {
  overflow: hidden;
  padding: var(--e-md) 0;
  border-block: 1px solid var(--borde);
  background: var(--superficie);
}
.cinta__pista {
  display: flex;
  align-items: center;
  gap: var(--e-xl);
  width: max-content;
  animation: desplaza 26s linear infinite;
  font-family: var(--display);
  font-size: 1.5rem;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: var(--tinta-suave);
}
.cinta__punto {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: var(--acento);
  flex-shrink: 0;
}
@keyframes desplaza {
  from { transform: translateX(0); }
  to   { transform: translateX(-50%); }
}

/* ------------------------------ 7. MANIFIESTO ------------------------------ */

.manifiesto {
  max-width: var(--ancho);
  margin-inline: auto;
  padding: var(--e-4xl) var(--margen);
  display: flex;
  flex-direction: column;
  gap: var(--e-xl);
}

.manifiesto__texto {
  max-width: 24ch;
  font-family: var(--display);
  font-size: clamp(2.25rem, 6vw, 5rem);
  line-height: 1.02;
  letter-spacing: 0.012em;
  text-transform: uppercase;
  text-wrap: balance;
  font-weight: 400;
}
.manifiesto__texto strong {
  color: var(--acento);
  font-weight: 400;
}
.manifiesto__texto em {
  font-style: normal;
  color: var(--acento);
}

.manifiesto__pie {
  display: flex;
  align-items: center;
  gap: var(--e-md);
}
.firma {
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  color: var(--tinta-suave);
  flex-shrink: 0;
}
.manifiesto__linea {
  flex: 1;
  height: 1px;
  background: var(--borde);
}

/* MARCA: estilos de la galeria -> feature/galeria-productos */

/* MARCA: estilos de tecnologia, cierre y pie -> feature/tecnologia-y-pie */

/* ------------------------------ 12. RESPONSIVE ------------------------------ */

@media (max-width: 1024px) {
  .galeria { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  .tarjeta--destacada { grid-column: span 2; grid-row: auto; }
  .bento { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  .bloque--ancho { grid-column: span 2; }
}

@media (max-width: 860px) {
  .nav__boton { display: flex; }

  .nav__menu {
    position: absolute;
    inset: 100% var(--margen) auto var(--margen);
    flex-direction: column;
    gap: 0;
    padding: var(--e-sm) 0;
    background: var(--superficie);
    border: 1px solid var(--borde);
    border-radius: var(--radio);
    opacity: 0;
    transform: translateY(-8px);
    pointer-events: none;
    transition: opacity var(--rapido) var(--curva),
                transform var(--rapido) var(--curva);
    transform-origin: top center;
  }
  .nav__menu.abierto {
    opacity: 1;
    transform: none;
    pointer-events: auto;
  }
  .nav__menu a { padding: 0.8rem var(--e-lg); }

  .hero { min-height: auto; }
  .hero__rejilla {
    grid-template-columns: 1fr;
    gap: var(--e-2xl);
  }
  .hero__visual { order: -1; max-width: 30rem; }
  .hero__pie { flex-direction: column; align-items: flex-start; }
  .hero__scroll { display: none; }
  .manifiesto__texto { max-width: 100%; }
}

@media (max-width: 620px) {
  .galeria,
  .bento { grid-template-columns: minmax(0, 1fr); }
  .tarjeta--destacada,
  .bloque--ancho { grid-column: span 1; }
  .datos { gap: var(--e-lg); }
  .datos__item dd { font-size: 1.6rem; }
  .boton { width: 100%; justify-content: center; }
}

/* ------------------------------ 13. MOVIMIENTO REDUCIDO ------------------------------ */

@media (prefers-reduced-motion: reduce) {
  html { scroll-behavior: auto; }

  *, *::before, *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }

  .js .reveal { opacity: 1; transform: none; }
  .cinta__pista { animation: none; }
  .ondas { display: none; }
}
FIN_DE_ARCHIVO_JBL
cat > index.html <<'FIN_DE_ARCHIVO_JBL'
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>JBL — El sonido que se siente</title>
  <meta name="description" content="JBL lleva casi 80 años construyendo altavoces. Conoce la línea de parlantes portátiles: JBL Pro Sound, resistencia IP67 y hasta 24 horas de batería.">
  <meta name="theme-color" content="#0a0a0b">

  <meta property="og:type" content="website">
  <meta property="og:title" content="JBL — El sonido que se siente">
  <meta property="og:description" content="Parlantes portátiles con JBL Pro Sound. Resistencia IP67, PartyBoost y hasta 24 horas de batería.">
  <meta property="og:locale" content="es_PE">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Source+Sans+3:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/estilos.css">

  <!-- Marca que JavaScript está activo. Las animaciones de entrada sólo se
       aplican si existe esta clase: sin JS la página se ve completa. -->
  <script>document.documentElement.classList.add('js');</script>
</head>
<body>

<a class="salto" href="#contenido">Saltar al contenido</a>

<!-- =========================================================
     Sprite de iconos y siluetas. Se define una sola vez aquí y
     se reutiliza con <use href="#id"> para no repetir los SVG.
     ========================================================= -->
<svg class="sprite" aria-hidden="true" focusable="false">
  <defs>
    <pattern id="rejilla" width="7" height="7" patternUnits="userSpaceOnUse">
      <rect width="7" height="7" fill="none"/>
      <circle cx="3.5" cy="3.5" r="1.2" fill="#4a4a56" fill-opacity=".55"/>
    </pattern>
    <!-- El cilindro está acostado, así que la luz cae de arriba abajo -->
    <linearGradient id="metal" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0" stop-color="#3a3a44"/>
      <stop offset="0.28" stop-color="#26262d"/>
      <stop offset="0.62" stop-color="#141418"/>
      <stop offset="1" stop-color="#0c0c0f"/>
    </linearGradient>
    <!-- Brillo especular sobre la parte alta del cuerpo -->
    <linearGradient id="brillo" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0" stop-color="#ffffff" stop-opacity=".14"/>
      <stop offset="0.34" stop-color="#ffffff" stop-opacity="0"/>
    </linearGradient>
  </defs>

  <!-- Silueta: parlante cilíndrico (Flip, Charge, Xtreme) -->
  <symbol id="fig-cilindro" viewBox="0 0 440 220">
    <ellipse cx="52" cy="110" rx="34" ry="96" fill="#24242b"/>
    <rect x="52" y="14" width="336" height="192" fill="url(#metal)"/>
    <rect x="52" y="14" width="336" height="192" fill="url(#rejilla)"/>
    <rect x="52" y="14" width="336" height="192" fill="url(#brillo)"/>
    <ellipse cx="388" cy="110" rx="34" ry="96" fill="#0b0b0d" stroke="#ff6a00" stroke-width="8"/>
    <ellipse cx="388" cy="110" rx="17" ry="50" fill="none" stroke="#ff6a00" stroke-width="2.5" opacity=".4"/>
  </symbol>




  <!-- Iconos de interfaz (trazo, estilo Lucide) -->
  <symbol id="ico-flecha" viewBox="0 0 24 24"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></symbol>
  <symbol id="ico-abajo" viewBox="0 0 24 24"><path d="M12 5v14"/><path d="m19 12-7 7-7-7"/></symbol>

  <!-- MARCA: siluetas de los parlantes -> feature/galeria-productos -->

  <!-- MARCA: iconos de tecnologia -> feature/tecnologia-y-pie -->
</svg>

<!-- ================= NAVEGACIÓN ================= -->
<header class="nav" id="nav">
  <div class="nav__caja">
    <a class="marca" href="#inicio" aria-label="JBL, ir al inicio">
      <span class="marca__sello" aria-hidden="true"></span>
      <span class="marca__texto">JBL</span>
    </a>

    <nav class="nav__menu" id="menu" aria-label="Navegación principal">
      <a href="#manifiesto">Marca</a>
      <a href="#productos">Parlantes</a>
      <a href="#tecnologia">Tecnología</a>
      <a href="#contacto">Contacto</a>
    </nav>

    <button class="nav__boton" id="navBoton" aria-label="Abrir menú" aria-expanded="false" aria-controls="menu">
      <span aria-hidden="true"></span>
      <span aria-hidden="true"></span>
    </button>
  </div>
</header>

<main id="contenido">

  <!-- ================= HERO ================= -->
  <section class="hero" id="inicio">
    <div class="hero__rejilla">
      <div class="hero__texto">
        <p class="etiqueta reveal">Audio portátil · Desde 1946</p>
        <h1 class="titular reveal">
          El sonido
          <span class="titular__acento">que se siente</span>
        </h1>
        <p class="hero__bajada reveal">
          Graves que empujan el pecho, agudos que no se rompen y una carcasa que
          aguanta la playa, la lluvia y la fiesta. Esto es JBL Pro&nbsp;Sound.
        </p>
        <div class="hero__acciones reveal">
          <a class="boton boton--solido" href="#productos">
            Ver los parlantes
            <svg class="boton__ico" aria-hidden="true"><use href="#ico-flecha"/></svg>
          </a>
          <a class="boton boton--linea" href="#manifiesto">Conocer la marca</a>
        </div>
      </div>

      <div class="hero__visual reveal">
        <div class="ondas" aria-hidden="true">
          <span></span><span></span><span></span>
        </div>
        <svg class="figura figura--hero" role="img" aria-label="Ilustración de un parlante portátil JBL">
          <use href="#fig-cilindro"/>
        </svg>
      </div>
    </div>

    <div class="hero__pie">
      <dl class="datos">
        <div class="datos__item"><dt>Potencia máxima</dt><dd><span data-contador="180">180</span> W</dd></div>
        <div class="datos__item"><dt>Batería</dt><dd>hasta <span data-contador="24">24</span> h</dd></div>
        <div class="datos__item"><dt>Resistencia</dt><dd>IP67</dd></div>
      </dl>
      <a class="hero__scroll" href="#manifiesto" aria-label="Bajar a la siguiente sección">
        <svg aria-hidden="true"><use href="#ico-abajo"/></svg>
      </a>
    </div>
  </section>

  <!-- ================= CINTA ================= -->
  <div class="cinta" aria-hidden="true">
    <div class="cinta__pista">
      <span>JBL Pro Sound</span><span class="cinta__punto"></span>
      <span>PartyBoost</span><span class="cinta__punto"></span>
      <span>Resistente al agua</span><span class="cinta__punto"></span>
      <span>Bluetooth 5.1</span><span class="cinta__punto"></span>
      <span>JBL Pro Sound</span><span class="cinta__punto"></span>
      <span>PartyBoost</span><span class="cinta__punto"></span>
      <span>Resistente al agua</span><span class="cinta__punto"></span>
      <span>Bluetooth 5.1</span><span class="cinta__punto"></span>
    </div>
  </div>

  <!-- ================= MANIFIESTO ================= -->
  <section class="manifiesto" id="manifiesto">
    <p class="etiqueta reveal">01 — La marca</p>
    <p class="manifiesto__texto reveal">
      Desde <strong>1946</strong> construimos altavoces para los que no se conforman
      con escuchar. El mismo sonido que llena estadios y salas de cine cabe hoy
      en una mano: <em>eso</em> es lo que hace JBL.
    </p>
    <div class="manifiesto__pie reveal">
      <span class="firma">James B. Lansing Sound</span>
      <span class="manifiesto__linea" aria-hidden="true"></span>
    </div>
  </section>

  <!-- MARCA: galeria de parlantes -> feature/galeria-productos -->

  <!-- MARCA: tecnologia y cierre -> feature/tecnologia-y-pie -->

</main>

<!-- MARCA: pie de pagina -> feature/tecnologia-y-pie -->


<script src="js/main.js"></script>
</body>
</html>
FIN_DE_ARCHIVO_JBL
git add css/estilos.css index.html
git commit -m 'fix: muestra el contenido aunque el navegador no ejecute JavaScript'
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
