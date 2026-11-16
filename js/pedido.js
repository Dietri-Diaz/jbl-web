/* =========================================================================
   JBL Store — Confirmación y seguimiento del pedido
   Dos usos de la misma página:
     · Recién comprado: llega con ?codigo=...&correo=... y muestra el pedido.
     · Seguimiento: el cliente escribe su código y su correo, y el ERP
       responde con el estado actual.
   ========================================================================= */

(() => {
  const { escapar, dinero } = JBL;

  const zona = document.querySelector('[data-pedido]');
  const formulario = document.getElementById('seguir');
  const estado = document.querySelector('[data-estado]');
  const PASOS = ['pendiente', 'confirmado', 'enviado', 'entregado'];

  const fecha = (iso) => new Date(iso).toLocaleDateString('es-PE', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' });

  const pasos = (pedido) => {
    if (pedido.estado === 'cancelado') {
      return '<p class="nota nota--error" style="margin:24px">Este pedido fue cancelado. Si tienes dudas, escríbenos desde la sección de contacto.</p>';
    }

    const actual = PASOS.indexOf(pedido.estado);
    const nombres = JBL.reglas().estados;

    return `
      <ol class="pasos" aria-label="Avance del pedido">
        ${PASOS.map((paso, i) => `
          <li class="${i < actual || pedido.estado === 'entregado' ? 'hecho' : (i === actual ? 'actual' : '')}"${i === actual ? ' aria-current="step"' : ''}>
            ${escapar(nombres[paso] || paso)}
          </li>`).join('')}
      </ol>`;
  };

  const pintar = (pedido, { nuevo = false, demo = false } = {}) => {
    const [prefijo, numero] = pedido.codigo.split('-');

    zona.innerHTML = `
      <header class="confirmacion__cab">
        <p class="etiqueta">${nuevo ? '¡Gracias por tu compra!' : 'Estado de tu pedido'}</p>
        <h1 class="confirmacion__codigo">${escapar(prefijo)}-<em>${escapar(numero || '')}</em></h1>
        <p class="bajada">${nuevo
          ? 'Guarda este código: con él y tu correo puedes seguir tu pedido cuando quieras.'
          : `Pedido de ${escapar(pedido.cliente)}, registrado el ${fecha(pedido.fecha)}.`}</p>
      </header>

      ${demo ? `<p class="nota" style="margin-bottom:20px"><svg class="ico" aria-hidden="true"><use href="#i-alerta"/></svg>
        <span><b>Pedido de demostración.</b> Esta versión del sitio no está conectada al sistema de la tienda:
        el pedido se guardó solo en este navegador y no aparecerá en el panel.</span></p>` : ''}

      <div class="compra" style="padding-block:0 40px">
        <div>
          <section class="caja" aria-label="Avance">${pasos(pedido)}</section>

          <section class="caja" aria-labelledby="t-productos">
            <div class="caja__cab"><h2 class="caja__titulo" id="t-productos">Productos</h2></div>
            <div class="caja__cuerpo">
              ${pedido.items.map((item) => `
                <div class="mini" style="grid-template-columns:minmax(0,1fr) auto">
                  <div><b>${escapar(item.nombre)}</b><span>${item.cantidad} × ${dinero(item.precio_unitario, true)}</span></div>
                  <strong>${dinero(item.subtotal)}</strong>
                </div>`).join('')}
              <dl class="totales" style="margin-top:16px">
                <div><dt>Subtotal</dt><dd>${dinero(pedido.subtotal)}</dd></div>
                <div><dt>Envío</dt><dd${pedido.envio === 0 ? ' class="gratis"' : ''}>${pedido.envio === 0 ? 'Gratis' : dinero(pedido.envio)}</dd></div>
                <div class="totales__total"><dt>Total</dt><dd>${dinero(pedido.total)}</dd></div>
              </dl>
            </div>
          </section>
        </div>

        <aside class="caja" aria-labelledby="t-detalle">
          <div class="caja__cab"><h2 class="caja__titulo" id="t-detalle">Detalle</h2></div>
          <div class="caja__cuerpo">
            <dl class="detalle" style="grid-template-columns:minmax(0,1fr)">
              <div><dt>Estado</dt><dd><b>${escapar(pedido.estado_nombre)}</b></dd></div>
              <div><dt>Fecha</dt><dd>${fecha(pedido.fecha)}</dd></div>
              <div><dt>Entrega</dt><dd>${escapar(pedido.entrega_nombre)}${pedido.distrito ? `<br>${escapar([pedido.direccion, pedido.distrito].filter(Boolean).join(', '))}` : ''}</dd></div>
              <div><dt>Pago</dt><dd>${escapar(pedido.pago_nombre)}</dd></div>
            </dl>
            <a class="boton boton--solido boton--bloque" href="catalogo.html" style="margin-top:24px">Seguir comprando</a>
          </div>
        </aside>
      </div>`;

    document.title = `Pedido ${pedido.codigo} — JBL Store`;
  };

  const consultar = async (codigo, correo) => {
    if (!JBL.ERP_URL) {
      estado.textContent = 'El seguimiento no está disponible en esta versión del sitio, porque no está conectada al sistema de la tienda.';
      return null;
    }

    try {
      const respuesta = await fetch(`${JBL.ERP_URL}/api/pedidos/${encodeURIComponent(codigo)}?correo=${encodeURIComponent(correo)}`, { headers: { Accept: 'application/json' } });
      if (respuesta.ok) return respuesta.json();

      estado.textContent = respuesta.status === 404
        ? 'No encontramos un pedido con ese código y ese correo. Revisa que estén bien escritos.'
        : 'No pudimos consultar tu pedido. Inténtalo otra vez en un momento.';
    } catch {
      estado.textContent = 'No pudimos consultar tu pedido porque el servidor no responde. Inténtalo más tarde.';
    }
    return null;
  };

  formulario.addEventListener('submit', async (evento) => {
    evento.preventDefault();
    estado.textContent = '';

    const codigo = formulario.elements.codigo.value.trim().toUpperCase();
    const correo = formulario.elements.correo.value.trim();
    if (!codigo || !correo) {
      estado.textContent = 'Escribe el código del pedido y tu correo.';
      return;
    }

    const boton = formulario.querySelector('button[type="submit"]');
    boton.disabled = true;
    const pedido = await consultar(codigo, correo);
    boton.disabled = false;

    if (pedido) {
      pintar(pedido);
      // El correo no se deja en la dirección: quedaría en el historial del navegador.
      history.replaceState(null, '', `?codigo=${encodeURIComponent(codigo)}`);
      zona.scrollIntoView({ block: 'start' });
    }
  });

  // ---- Arranque: ¿llegó un pedido en la dirección? -------------------------
  const codigo = JBL.parametro('codigo');
  const correo = JBL.parametro('correo');

  let reciente = null;
  try { reciente = JSON.parse(sessionStorage.getItem('jbl-ultimo-pedido')); } catch { /* sin almacenamiento */ }

  if (codigo && reciente && reciente.pedido.codigo === codigo) {
    // Recién comprado: se muestra al instante lo que respondió el ERP.
    pintar(reciente.pedido, { nuevo: true, demo: reciente.demo });
    // Recién comprado no hace falta el buscador: ya se está viendo el pedido.
    document.querySelector('[data-buscador]').hidden = true;
  } else if (codigo && correo) {
    formulario.elements.codigo.value = codigo;
    formulario.elements.correo.value = correo;
    consultar(codigo, correo).then((pedido) => { if (pedido) pintar(pedido); });
    history.replaceState(null, '', `?codigo=${encodeURIComponent(codigo)}`);
  } else if (codigo) {
    // Llegó solo el código: se deja escrito y falta el correo para consultarlo.
    formulario.elements.codigo.value = codigo;
  }
})();
