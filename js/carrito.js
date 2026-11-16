/* =========================================================================
   JBL Store — Página del carrito
   Lista los productos elegidos, permite cambiar cantidades o quitarlos y
   muestra el resumen con el envío.
   ========================================================================= */

(() => {
  const { escapar, dinero, icono } = JBL;
  const zona = document.querySelector('[data-carrito]');

  const linea = (l) => {
    const p = l.producto;
    const tope = Math.max(1, JBL.tope(p));

    return `
      <li class="linea">
        ${p.foto ? `<img class="linea__foto" src="${escapar(p.foto)}" alt="${escapar(p.foto_alt)}" width="96" height="96" loading="lazy">` : '<span class="linea__foto"></span>'}
        <div>
          <p class="linea__nombre"><a href="producto.html?p=${encodeURIComponent(p.slug)}">${escapar(p.nombre)}</a></p>
          <p class="linea__unitario">${dinero(p.precio_final, true)} c/u</p>
          <div class="linea__controles">
            <div class="cantidad cantidad--chica" role="group" aria-label="Cantidad de ${escapar(p.nombre)}">
              <button type="button" data-cambiar="${p.id}" data-a="${l.cantidad - 1}" aria-label="Quitar una unidad" ${l.cantidad <= 1 ? 'disabled' : ''}>${icono('menos')}</button>
              <output>${l.cantidad}</output>
              <button type="button" data-cambiar="${p.id}" data-a="${l.cantidad + 1}" aria-label="Agregar una unidad" ${l.cantidad >= tope ? 'disabled' : ''}>${icono('mas')}</button>
            </div>
            <button class="linea__quitar" type="button" data-quitar="${p.id}">Quitar<span class="sr-only"> ${escapar(p.nombre)}</span></button>
          </div>
        </div>
        <p class="linea__total">${dinero(l.subtotal, true)}</p>
        ${l.excede ? `<p class="linea__aviso">${p.stock === 0 ? 'Este producto se agotó: quítalo para continuar.' : `Solo quedan ${p.stock}: reduce la cantidad para continuar.`}</p>` : ''}
      </li>`;
  };

  const pintar = () => {
    const lineas = JBL.carrito.lineas();

    if (lineas.length === 0) {
      zona.innerHTML = `
        <div class="vacio">
          <p class="vacio__titulo">Tu carrito está vacío</p>
          <p>Cuando agregues un producto aparecerá aquí, listo para comprar.</p>
          <a class="boton boton--solido boton--grande" href="catalogo.html">Ver el catálogo</a>
        </div>`;
      return;
    }

    const reglas = JBL.reglas();
    const { subtotal, envio, total, falta } = JBL.carrito.totales('delivery');
    const avance = Math.min(1, subtotal / reglas.envio_gratis_desde);
    const bloqueado = lineas.some((l) => l.excede);

    zona.innerHTML = `
      <div class="compra">
        <section class="caja" aria-label="Productos del carrito">
          <ul>${lineas.map(linea).join('')}</ul>
        </section>

        <aside class="caja resumen" aria-labelledby="t-resumen">
          <div class="caja__cab"><h2 class="caja__titulo" id="t-resumen">Resumen</h2></div>
          <div class="caja__cuerpo">
            <div class="progreso">
              ${falta > 0 ? `Te faltan <b>${dinero(falta)}</b> para el envío gratis.` : '<b>Tu pedido tiene envío gratis.</b>'}
              <div class="progreso__barra" aria-hidden="true"><i style="transform:scaleX(${avance})"></i></div>
            </div>
            <dl class="totales">
              <div><dt>Subtotal (${JBL.carrito.unidades()} ${JBL.carrito.unidades() === 1 ? 'unidad' : 'unidades'})</dt><dd>${dinero(subtotal)}</dd></div>
              <div><dt>Envío a domicilio</dt><dd${envio === 0 ? ' class="gratis"' : ''}>${envio === 0 ? 'Gratis' : dinero(envio)}</dd></div>
              <div class="totales__total"><dt>Total</dt><dd>${dinero(total)}</dd></div>
            </dl>
            ${bloqueado
              ? '<p class="nota nota--error" style="margin-top:20px">Ajusta los productos marcados para continuar.</p>'
              : '<a class="boton boton--solido boton--grande boton--bloque" href="checkout.html">Finalizar compra</a>'}
            <p class="resumen__nota">Con recojo en tienda no pagas envío. Lo eliges en el siguiente paso.</p>
            <a class="boton boton--linea boton--bloque" href="catalogo.html" style="margin-top:12px">Seguir comprando</a>
          </div>
        </aside>
      </div>`;
  };

  zona.addEventListener('click', (evento) => {
    const cambiar = evento.target.closest('[data-cambiar]');
    if (cambiar) JBL.carrito.cambiar(cambiar.dataset.cambiar, Number(cambiar.dataset.a));

    const quitar = evento.target.closest('[data-quitar]');
    if (quitar) {
      const p = JBL.productoPorId(quitar.dataset.quitar);
      JBL.carrito.quitar(quitar.dataset.quitar);
      JBL.avisar(`Quitaste ${p ? p.nombre : 'el producto'} del carrito.`);
    }
  });

  // El carrito avisa cada vez que cambia, también si cambió en otra pestaña.
  document.addEventListener('jbl:carrito', pintar);
  JBL.alCargar(pintar);
})();
