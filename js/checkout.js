/* =========================================================================
   JBL Store — Finalizar compra
   Valida el formulario, envía el pedido al ERP y lleva a la página de
   confirmación. El ERP vuelve a validar todo y calcula los precios: lo que
   sale del navegador solo dice qué productos y cuántos.

   Sin ERP (sitio publicado en Vercel), el pedido se guarda solo en este
   navegador como demostración, y se avisa con claridad.
   ========================================================================= */

(() => {
  const { escapar, dinero } = JBL;

  const formulario = document.querySelector('[data-pago]');
  const vacio = document.querySelector('[data-vacio]');
  const errorGeneral = document.querySelector('[data-error-general]');
  const boton = document.querySelector('[data-confirmar]');
  const zonaDireccion = document.querySelector('[data-direccion]');

  const DISTRITOS = [
    'Ate', 'Barranco', 'Breña', 'Callao', 'Carabayllo', 'Cercado de Lima', 'Chorrillos', 'Comas', 'El Agustino',
    'Independencia', 'Jesús María', 'La Molina', 'La Victoria', 'Lince', 'Los Olivos', 'Magdalena del Mar',
    'Miraflores', 'Pueblo Libre', 'Puente Piedra', 'Rímac', 'San Borja', 'San Isidro', 'San Juan de Lurigancho',
    'San Juan de Miraflores', 'San Luis', 'San Martín de Porres', 'San Miguel', 'Santa Anita', 'Santiago de Surco',
    'Surquillo', 'Villa El Salvador', 'Villa María del Triunfo',
  ];
  formulario.elements.distrito.insertAdjacentHTML('beforeend', DISTRITOS.map((d) => `<option>${d}</option>`).join(''));

  let enviando = false;
  const entrega = () => formulario.elements.entrega.value;

  // ---- Resumen ------------------------------------------------------------
  const pintar = () => {
    if (enviando) return;

    const lineas = JBL.carrito.lineas();
    const hayCompra = lineas.length > 0;
    formulario.hidden = !hayCompra;
    vacio.hidden = hayCompra;
    if (!hayCompra) return;

    const reglas = JBL.reglas();
    const { subtotal, envio, total } = JBL.carrito.totales(entrega());

    document.querySelector('[data-resumen-lineas]').innerHTML = lineas.map((l) => `
      <div class="mini">
        ${l.producto.foto ? `<img src="${escapar(l.producto.foto)}" alt="" width="56" height="56" loading="lazy">` : '<span></span>'}
        <div><b>${escapar(l.producto.nombre)}</b><span>${l.cantidad} × ${dinero(l.producto.precio_final, true)}</span></div>
        <strong>${dinero(l.subtotal, true)}</strong>
      </div>`).join('');

    document.querySelector('[data-resumen-totales]').innerHTML = `
      <div><dt>Subtotal</dt><dd>${dinero(subtotal)}</dd></div>
      <div><dt>${entrega() === 'recojo' ? 'Recojo en tienda' : 'Envío a domicilio'}</dt><dd${envio === 0 ? ' class="gratis"' : ''}>${envio === 0 ? 'Gratis' : dinero(envio)}</dd></div>
      <div class="totales__total"><dt>Total</dt><dd>${dinero(total)}</dd></div>`;

    document.querySelector('[data-texto-envio]').textContent = subtotal >= reglas.envio_gratis_desde
      ? 'Lima Metropolitana · Gratis'
      : `Lima Metropolitana · ${dinero(reglas.envio, true)}`;

    zonaDireccion.hidden = entrega() === 'recojo';

    document.querySelector('[data-modo]').textContent = JBL.origen === 'erp'
      ? 'Tu pedido quedará registrado y recibirás un código de seguimiento.'
      : 'Si el sistema de la tienda no responde, el pedido se guardará solo en este navegador, como demostración.';
  };

  // ---- Validación en el navegador ----------------------------------------
  const marcar = (campo, mensaje) => {
    const entrada = formulario.elements[campo];
    const error = formulario.querySelector(`[data-error="${campo}"]`);
    if (error) error.textContent = mensaje;
    if (entrada && entrada.setAttribute) entrada.setAttribute('aria-invalid', 'true');
  };

  const limpiar = () => {
    formulario.querySelectorAll('[data-error]').forEach((el) => { el.textContent = ''; });
    formulario.querySelectorAll('[aria-invalid]').forEach((el) => el.removeAttribute('aria-invalid'));
    errorGeneral.hidden = true;
  };

  const validar = (d) => {
    const errores = {};
    if (d.nombre.trim().length < 3) errores.nombre = 'Escribe tu nombre completo.';
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(d.correo)) errores.correo = 'Escribe un correo válido.';
    if (!/^9\d{8}$/.test(d.telefono)) errores.telefono = 'El celular debe tener 9 dígitos y empezar con 9.';
    if (d.documento && !/^(\d{8}|\d{11})$/.test(d.documento)) errores.documento = 'Debe ser un DNI (8 dígitos) o un RUC (11 dígitos).';
    if (d.entrega === 'delivery') {
      if (d.direccion.trim().length < 5) errores.direccion = 'Escribe la dirección de entrega.';
      if (!d.distrito) errores.distrito = 'Elige el distrito de entrega.';
    }
    return errores;
  };

  const mostrarErrores = (errores) => {
    Object.entries(errores).forEach(([campo, mensaje]) => marcar(campo, mensaje));
    formulario.querySelector('[aria-invalid="true"]')?.focus();
  };

  // ---- Envío --------------------------------------------------------------
  const terminar = (pedido, correo, demo = false) => {
    // Se guarda para que la página de confirmación lo muestre sin volver a pedirlo.
    try { sessionStorage.setItem('jbl-ultimo-pedido', JSON.stringify({ pedido, correo, demo })); } catch { /* sin almacenamiento */ }
    JBL.carrito.vaciar();
    location.href = `pedido.html?codigo=${encodeURIComponent(pedido.codigo)}${demo ? '&demo=1' : ''}`;
  };

  /** Pedido de demostración: se arma en el navegador con los mismos cálculos. */
  const pedidoDemo = (d) => {
    const reglas = JBL.reglas();
    const { subtotal, envio, total } = JBL.carrito.totales(d.entrega);

    return {
      codigo: `DEMO-${String(Date.now()).slice(-6)}`,
      estado: 'pendiente',
      estado_nombre: 'Pendiente',
      fecha: new Date().toISOString(),
      cliente: d.nombre,
      entrega: d.entrega,
      entrega_nombre: reglas.entregas[d.entrega] || d.entrega,
      direccion: d.entrega === 'delivery' ? d.direccion : null,
      distrito: d.entrega === 'delivery' ? d.distrito : null,
      pago: d.pago,
      pago_nombre: reglas.pagos[d.pago] || d.pago,
      subtotal, envio, total,
      items: JBL.carrito.lineas().map((l) => ({
        nombre: l.producto.nombre, cantidad: l.cantidad, precio_unitario: l.producto.precio_final, subtotal: l.subtotal,
      })),
    };
  };

  formulario.addEventListener('submit', async (evento) => {
    evento.preventDefault();
    limpiar();

    const d = Object.fromEntries(new FormData(formulario));
    const errores = validar(d);
    if (Object.keys(errores).length) return mostrarErrores(errores);

    if (JBL.carrito.lineas().some((l) => l.excede)) {
      errorGeneral.textContent = 'Algunos productos ya no tienen stock suficiente. Revisa tu carrito.';
      errorGeneral.hidden = false;
      return;
    }

    // Sin dirección de ERP (sitio publicado): pedido de demostración en el navegador.
    if (!JBL.ERP_URL) return terminar(pedidoDemo(d), d.correo, true);

    // Con un ERP configurado siempre se intenta registrar el pedido de verdad,
    // aunque el catálogo haya tardado en llegar al abrir la página.

    enviando = true;
    boton.disabled = true;
    boton.textContent = 'Registrando tu pedido…';

    try {
      const respuesta = await fetch(`${JBL.ERP_URL}/api/pedidos`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
        body: JSON.stringify({
          ...d,
          items: JBL.carrito.lineas().map((l) => ({ id: l.producto.id, cantidad: l.cantidad })),
        }),
      });

      if (respuesta.status === 201) return terminar(await respuesta.json(), d.correo);

      if (respuesta.status === 422) {
        const { errors = {} } = await respuesta.json();
        const deCampos = {};
        const deProductos = [];
        Object.entries(errors).forEach(([campo, mensajes]) => {
          if (campo.startsWith('items')) deProductos.push(mensajes[0]); else deCampos[campo] = mensajes[0];
        });
        if (deProductos.length) {
          errorGeneral.textContent = `${[...new Set(deProductos)].join(' ')} Revisa tu carrito.`;
          errorGeneral.hidden = false;
          errorGeneral.scrollIntoView({ block: 'center' });
        }
        mostrarErrores(deCampos);
      } else if (respuesta.status === 429) {
        errorGeneral.textContent = 'Hiciste varios pedidos seguidos. Espera un minuto e inténtalo otra vez.';
        errorGeneral.hidden = false;
      } else {
        throw new Error(`Respuesta ${respuesta.status}`);
      }
    } catch {
      // El ERP nunca respondió en esta visita: la tienda está trabajando con la
      // copia estática, así que el pedido queda como demostración.
      if (JBL.origen !== 'erp') return terminar(pedidoDemo(d), d.correo, true);

      errorGeneral.textContent = 'No pudimos registrar tu pedido porque el servidor no responde. Tu carrito sigue guardado: inténtalo otra vez en un momento.';
      errorGeneral.hidden = false;
      errorGeneral.scrollIntoView({ block: 'center' });
    }

    enviando = false;
    boton.disabled = false;
    boton.textContent = 'Confirmar pedido';
  });

  // Solo dígitos en celular y documento.
  ['telefono', 'documento'].forEach((campo) => {
    formulario.elements[campo].addEventListener('input', (e) => { e.target.value = e.target.value.replace(/\D/g, ''); });
  });

  formulario.addEventListener('change', (e) => { if (e.target.name === 'entrega') pintar(); });
  document.addEventListener('jbl:carrito', pintar);
  JBL.alCargar(pintar);
})();
