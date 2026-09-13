/* =========================================================================
   JBL — Landing publicitaria
   Comportamiento de la pagina.
   ========================================================================= */

document.addEventListener('DOMContentLoaded', () => {

  /* ---------------------------------------------------------------------
     Barra de navegación: se vuelve sólida al bajar
     --------------------------------------------------------------------- */
  const nav = document.getElementById('nav');

  const actualizarNav = () => {
    nav.classList.toggle('compacta', window.scrollY > 40);
  };

  actualizarNav();
  window.addEventListener('scroll', actualizarNav, { passive: true });

});
