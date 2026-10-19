/* =========================================================================
   JBL — Catálogo desde el ERP
   Si el ERP está encendido, la galería muestra los parlantes que se
   administran allí (solo los publicados y en su orden). Si no responde,
   se queda el catálogo escrito en el HTML: la página nunca se ve vacía.
   ========================================================================= */

(() => {
  // Dirección del ERP. Se puede cambiar con <html data-erp="https://...">.
  const ERP_URL = document.documentElement.dataset.erp || 'http://127.0.0.1:8000';
  const ESPERA_MAXIMA_MS = 2500;
  const NUMEROS = ['Ningún', 'Un', 'Dos', 'Tres', 'Cuatro', 'Cinco', 'Seis', 'Siete', 'Ocho', 'Nueve', 'Diez'];

  // Los datos vienen de un formulario: se escapan antes de ponerlos en el HTML.
  const escapar = (texto) => String(texto ?? '').replace(/[&<>"']/g, (c) => (
    { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]
  ));

  // Misma estructura que las tarjetas escritas en index.html.
  const tarjeta = (p) => `
    <article id="${escapar(p.slug)}" class="tarjeta${p.sello ? ' tarjeta--destacada' : ''} reveal visible">
      ${p.sello ? `<p class="tarjeta__sello">${escapar(p.sello)}</p>` : ''}
      <div class="tarjeta__foto">
        ${p.foto_url ? `<img src="${escapar(p.foto_url)}" alt="${escapar(p.foto_alt)}" loading="lazy" width="900" height="675">` : ''}
      </div>
      <div class="tarjeta__cuerpo">
        <p class="tarjeta__tipo">${escapar(p.tipo)}</p>
        <h3 class="tarjeta__nombre">${escapar(p.nombre)}</h3>
        <ul class="specs">
          <li><span>Potencia</span><strong>${escapar(p.potencia_w)} W</strong></li>
          <li><span>Batería</span><strong>${escapar(p.bateria_h)} h</strong></li>
          <li><span>Resistencia</span><strong>${escapar(p.resistencia)}</strong></li>
        </ul>
      </div>
    </article>`;

  document.addEventListener('DOMContentLoaded', async () => {
    const galeria = document.querySelector('#parlantes .galeria');
    if (!galeria) return;

    // Si el ERP tarda demasiado, se cancela y se queda el catálogo del HTML.
    const control = new AbortController();
    const reloj = setTimeout(() => control.abort(), ESPERA_MAXIMA_MS);

    try {
      const respuesta = await fetch(`${ERP_URL}/api/parlantes`, {
        headers: { Accept: 'application/json' },
        signal: control.signal,
      });
      if (!respuesta.ok) return;

      const { data } = await respuesta.json();
      if (!Array.isArray(data) || data.length === 0) return;

      galeria.innerHTML = data.map(tarjeta).join('');
      galeria.dataset.origen = 'erp';

      // "Seis modelos, del que cabe..." pasa a decir cuántos hay de verdad.
      const texto = document.querySelector('#parlantes .cabecera__texto');
      if (texto) {
        const n = data.length;
        const cantidad = n === 1 ? 'Un modelo' : `${NUMEROS[n] || n} modelos`;
        texto.innerHTML = texto.innerHTML.replace(/Seis modelos/, cantidad);
      }
    } catch {
      // El ERP no está encendido: se queda el catálogo escrito en el HTML.
    } finally {
      clearTimeout(reloj);
    }
  });
})();
