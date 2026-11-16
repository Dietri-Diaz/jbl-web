/* =========================================================================
   JBL Store — Formulario de contacto
   Envía el mensaje al ERP (Laravel) por su API. Si no hay un ERP
   conectado, avisa con un mensaje claro en vez de fallar en silencio.
   ========================================================================= */

(() => {
  const formulario = document.getElementById('contacto-form');
  if (!formulario) return;

  const estado = formulario.querySelector('.estado-form');
  const boton = formulario.querySelector('button[type="submit"]');

  const avisar = (texto, tipo) => {
    estado.textContent = texto;
    estado.className = `estado-form estado-form--${tipo}`;
  };

  const limpiarErrores = () => {
    formulario.querySelectorAll('[data-error]').forEach((el) => { el.textContent = ''; });
    formulario.querySelectorAll('[aria-invalid]').forEach((el) => el.removeAttribute('aria-invalid'));
  };

  formulario.addEventListener('submit', async (evento) => {
    evento.preventDefault();
    limpiarErrores();

    // Sitio publicado sin ERP conectado: se avisa en vez de intentar el envío.
    if (!JBL.ERP_URL) {
      avisar('El formulario aún no está conectado en esta versión del sitio. Inténtalo más tarde.', 'error');
      return;
    }

    const datos = Object.fromEntries(new FormData(formulario));
    boton.disabled = true;
    avisar('Enviando tu mensaje…', 'info');

    try {
      const respuesta = await fetch(`${JBL.ERP_URL}/api/mensajes`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
        body: JSON.stringify(datos),
      });

      if (respuesta.status === 201) {
        formulario.reset();
        avisar('¡Gracias! Recibimos tu mensaje y te responderemos pronto.', 'ok');
      } else if (respuesta.status === 422) {
        // Errores de validación: se muestran debajo de cada campo.
        const { errors = {} } = await respuesta.json();
        Object.entries(errors).forEach(([campo, mensajes]) => {
          const error = formulario.querySelector(`[data-error="${campo}"]`);
          const entrada = formulario.elements[campo];
          if (error) error.textContent = mensajes[0];
          if (entrada) entrada.setAttribute('aria-invalid', 'true');
        });
        avisar('Revisa los campos marcados.', 'error');
        formulario.querySelector('[aria-invalid="true"]')?.focus();
      } else if (respuesta.status === 429) {
        avisar('Enviaste varios mensajes seguidos. Espera un minuto e inténtalo otra vez.', 'error');
      } else {
        throw new Error(`Respuesta ${respuesta.status}`);
      }
    } catch {
      avisar('No pudimos enviar tu mensaje porque el servidor no responde. Inténtalo más tarde.', 'error');
    } finally {
      boton.disabled = false;
    }
  });
})();
