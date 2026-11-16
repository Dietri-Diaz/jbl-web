/* =========================================================================
   JBL Store — Página de inicio
   Llena con datos del catálogo: la ficha de la portada, las categorías,
   los destacados y las ofertas.
   ========================================================================= */

(() => {
  const { escapar, dinero, tarjeta } = JBL;

  JBL.alCargar((catalogo) => {
    const productos = catalogo.productos;
    const reglas = JBL.reglas();

    // ---- Ficha flotante de la portada: el primer producto destacado ----
    const estrella = productos.find((p) => p.destacado && p.stock > 0) || productos[0];
    const ficha = document.querySelector('[data-portada-ficha]');
    if (ficha && estrella) {
      ficha.href = `producto.html?p=${encodeURIComponent(estrella.slug)}`;
      ficha.innerHTML = `
        <small>Destacado</small>
        <b>${escapar(estrella.nombre)}</b>
        <span>${dinero(estrella.precio_final, true)}</span>`;
      ficha.hidden = false;
    }

    // ---- Categorías: la foto es la del primer producto de cada una ----
    const categorias = document.querySelector('[data-categorias]');
    if (categorias) {
      categorias.innerHTML = catalogo.categorias.map((c) => {
        const muestra = productos.find((p) => p.categoria === c.slug && p.foto);
        return `
          <a class="cat" href="catalogo.html?categoria=${encodeURIComponent(c.slug)}">
            ${muestra ? `<img src="${escapar(muestra.foto)}" alt="" loading="lazy" width="600" height="600">` : ''}
            <span class="cat__texto">
              <span class="cat__nombre">${escapar(c.nombre)}</span>
              <span class="cat__total">${c.total} ${c.total === 1 ? 'producto' : 'productos'} ${JBL.icono('flecha')}</span>
            </span>
          </a>`;
      }).join('');
    }

    // ---- Destacados ----
    const destacados = document.querySelector('[data-destacados]');
    if (destacados) {
      const lista = productos.filter((p) => p.destacado).slice(0, 4);
      destacados.innerHTML = (lista.length ? lista : productos.slice(0, 4)).map(tarjeta).join('');
    }

    // ---- Ofertas: la sección se oculta si no hay ninguna ----
    const ofertas = document.querySelector('[data-ofertas]');
    const seccionOfertas = document.querySelector('[data-seccion-ofertas]');
    if (ofertas) {
      const lista = productos.filter((p) => p.precio_oferta).sort((a, b) => b.descuento - a.descuento).slice(0, 4);
      ofertas.innerHTML = lista.map(tarjeta).join('');
      seccionOfertas.hidden = lista.length === 0;
    }

    const banda = document.querySelector('[data-banda-envio]');
    if (banda) banda.textContent = `Envío gratis en Lima en compras desde ${dinero(reglas.envio_gratis_desde, true)}.`;
  });
})();
