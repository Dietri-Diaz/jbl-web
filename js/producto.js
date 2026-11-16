/* =========================================================================
   JBL Store — Ficha de producto
   Lee el producto de la dirección (producto.html?p=flip-5) y arma la ficha:
   foto, precio, stock, cantidad, ficha técnica y productos relacionados.
   ========================================================================= */

(() => {
  const { escapar, dinero, icono, tarjeta } = JBL;

  const zona = document.querySelector('[data-ficha]');
  const relacionados = document.querySelector('[data-relacionados]');
  const seccionRelacionados = document.querySelector('[data-relacionados-seccion]');
  const slug = JBL.parametro('p');

  let cantidad = 1;

  const noEncontrado = () => {
    zona.innerHTML = `
      <div class="vacio">
        <p class="vacio__titulo">No encontramos ese producto</p>
        <p>Puede que ya no esté publicado o que la dirección tenga un error.</p>
        <a class="boton boton--oscuro" href="catalogo.html">Ver el catálogo</a>
      </div>`;
    seccionRelacionados.hidden = true;
  };

  const stock = (p) => {
    if (p.stock === 0) return '<p class="ficha__stock ficha__stock--agotado">Agotado</p>';
    if (p.stock <= 5) return `<p class="ficha__stock ficha__stock--bajo">${p.stock === 1 ? 'Queda 1 unidad' : `Quedan ${p.stock} unidades`}</p>`;
    return '<p class="ficha__stock ficha__stock--ok">Disponible</p>';
  };

  const specs = (p) => {
    const datos = [
      ['Potencia', p.potencia_w ? `${p.potencia_w} W` : ''],
      ['Batería', p.bateria_h ? `${p.bateria_h} h` : ''],
      ['Resistencia', p.resistencia || ''],
      ['Color', p.color || ''],
    ].filter(([, valor]) => valor);

    return datos.length ? `
      <dl class="specs">
        ${datos.map(([nombre, valor]) => `<div class="spec"><dt>${nombre}</dt><dd>${escapar(valor)}</dd></div>`).join('')}
      </dl>` : '';
  };

  const pintar = (catalogo) => {
    const p = JBL.producto(slug);
    if (!p) return noEncontrado();

    const reglas = JBL.reglas();
    const tope = JBL.tope(p);
    cantidad = Math.max(1, Math.min(cantidad, Math.max(1, tope)));
    document.title = `${p.nombre} — JBL Store`;

    zona.innerHTML = `
      <div class="ficha">
        <div class="ficha__foto">
          ${p.foto ? `<img src="${escapar(p.foto)}" alt="${escapar(p.foto_alt)}" width="900" height="900" fetchpriority="high">` : ''}
          ${JBL.sellos(p)}
        </div>

        <div>
          <nav class="miga miga--clara" aria-label="Ruta">
            <a href="index.html">Inicio</a><span aria-hidden="true">/</span>
            <a href="catalogo.html?categoria=${encodeURIComponent(p.categoria)}">${escapar(p.categoria_nombre)}</a><span aria-hidden="true">/</span>
            <span aria-current="page">${escapar(p.nombre)}</span>
          </nav>

          <h1 class="ficha__nombre">${escapar(p.nombre)}</h1>
          <p class="ficha__tipo">${escapar(p.tipo)}${p.sku ? ` · Código ${escapar(p.sku)}` : ''}</p>

          <div class="ficha__precio">
            <span class="precio__ahora">${dinero(p.precio_final, true)}</span>
            ${p.precio_oferta ? `<span class="precio__antes"><span class="sr-only">Antes </span>${dinero(p.precio, true)}</span>
              <span class="sello sello--oferta">Ahorras ${dinero(p.precio - p.precio_final, true)}</span>` : ''}
          </div>
          ${stock(p)}

          ${p.descripcion ? `<p class="ficha__descripcion">${escapar(p.descripcion)}</p>` : ''}

          <div class="ficha__compra">
            <div class="cantidad" role="group" aria-label="Cantidad">
              <button type="button" data-menos aria-label="Quitar una unidad" ${cantidad <= 1 ? 'disabled' : ''}>${icono('menos')}</button>
              <output aria-live="polite">${cantidad}</output>
              <button type="button" data-mas aria-label="Agregar una unidad" ${cantidad >= tope ? 'disabled' : ''}>${icono('mas')}</button>
            </div>
            <button class="boton boton--solido boton--grande" type="button" data-comprar ${p.stock === 0 ? 'disabled' : ''}>
              ${p.stock === 0 ? 'Sin stock' : 'Agregar al carrito'}
            </button>
          </div>

          ${specs(p)}

          <ul class="garantias">
            <li>${icono('camion')}<span>Envío a Lima Metropolitana por ${dinero(reglas.envio, true)}, gratis desde ${dinero(reglas.envio_gratis_desde, true)}.</span></li>
            <li>${icono('tienda')}<span>Recojo en tienda sin costo.</span></li>
            <li>${icono('billete')}<span>Pagas contra entrega, con Yape o Plin, o por transferencia.</span></li>
          </ul>
        </div>
      </div>`;

    const otros = catalogo.productos.filter((o) => o.categoria === p.categoria && o.id !== p.id).slice(0, 4);
    relacionados.innerHTML = otros.map(tarjeta).join('');
    seccionRelacionados.hidden = otros.length === 0;
  };

  zona.addEventListener('click', (evento) => {
    const p = JBL.producto(slug);
    if (!p) return;

    // Tras volver a pintar, el foco regresa al botón usado; si quedó
    // deshabilitado (se llegó al mínimo o al tope), pasa al otro.
    const enfocar = (usado, otro) => {
      const boton = zona.querySelector(usado);
      (boton.disabled ? zona.querySelector(otro) : boton).focus();
    };
    if (evento.target.closest('[data-menos]')) { cantidad -= 1; pintar(JBL.catalogo); enfocar('[data-menos]', '[data-mas]'); }
    if (evento.target.closest('[data-mas]')) { cantidad += 1; pintar(JBL.catalogo); enfocar('[data-mas]', '[data-menos]'); }

    if (evento.target.closest('[data-comprar]')) {
      const agregadas = JBL.carrito.agregar(p.id, cantidad);
      if (agregadas > 0) {
        JBL.avisar(
          agregadas === 1 ? `${p.nombre} está en tu carrito.` : `${agregadas} unidades de ${p.nombre} en tu carrito.`,
          { enlace: 'carrito.html', textoEnlace: 'Ver carrito' },
        );
      } else {
        JBL.avisar(`Ya tienes en el carrito el máximo disponible de ${p.nombre}.`, { tipo: 'error' });
      }
    }
  });

  JBL.alCargar(pintar);
})();
