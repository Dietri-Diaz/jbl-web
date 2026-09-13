/* =========================================================================
   JBL — Landing publicitaria
   Sólo dos comportamientos: el menú en móvil y una aparición suave de los
   bloques al hacer scroll. El resto del movimiento vive en el CSS.
   ========================================================================= */

document.addEventListener('DOMContentLoaded', () => {

  const sinMovimiento = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------------------------------------------------------------------
     1. La barra se vuelve sólida al bajar
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

  menu.querySelectorAll('a').forEach((enlace) => {
    enlace.addEventListener('click', cerrarMenu);
  });

  document.addEventListener('keydown', (evento) => {
    if (evento.key === 'Escape') cerrarMenu();
  });

  /* ---------------------------------------------------------------------
     3. Aparición de los bloques al entrar en pantalla
     IntersectionObserver avisa cuando un elemento entra en el viewport,
     sin tener que escuchar el scroll todo el tiempo.
     --------------------------------------------------------------------- */
  const elementos = document.querySelectorAll('.reveal');

  if (sinMovimiento) {
    elementos.forEach((el) => el.classList.add('visible'));
    return;
  }

  const observador = new IntersectionObserver((entradas) => {
    entradas.forEach((entrada, indice) => {
      if (!entrada.isIntersecting) return;

      // Cascada corta: los elementos de un mismo bloque entran escalonados.
      entrada.target.style.transitionDelay = `${Math.min(indice, 6) * 60}ms`;
      entrada.target.classList.add('visible');

      observador.unobserve(entrada.target);
    });
  }, {
    threshold: 0.1,
    rootMargin: '0px 0px -60px 0px'
  });

  elementos.forEach((el) => observador.observe(el));

});
