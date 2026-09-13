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
