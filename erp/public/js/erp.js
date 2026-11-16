/* =========================================================================
   JBL ERP — Comportamiento del panel
   Sin dependencias, salvo Chart.js para los gráficos del inicio.

     1. Menú lateral en pantallas pequeñas
     2. Avisos que se cierran solos
     3. Diálogo de confirmación
     4. Vista previa de la foto
     5. Menú del usuario
     6. Gráficos
   ========================================================================= */

(() => {
  'use strict';

  // ---- 1. Menú lateral en pantallas pequeñas ------------------------------
  const erp = document.getElementById('erp');
  const abrir = document.querySelector('[data-menu-abrir]');

  const alternarMenu = (abierto) => {
    erp.classList.toggle('menu-abierto', abierto);
    abrir.setAttribute('aria-expanded', String(abierto));
  };

  if (erp && abrir) {
    abrir.addEventListener('click', () => alternarMenu(!erp.classList.contains('menu-abierto')));
    document.querySelector('[data-menu-cerrar]')?.addEventListener('click', () => alternarMenu(false));
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') alternarMenu(false); });
  }

  // ---- 2. Avisos que se cierran solos -------------------------------------
  const cerrarAviso = (aviso) => {
    aviso.classList.add('saliendo');
    setTimeout(() => aviso.remove(), 220);
  };

  document.querySelectorAll('[data-aviso]').forEach((aviso) => {
    aviso.querySelector('[data-aviso-cerrar]')?.addEventListener('click', () => cerrarAviso(aviso));
    // Los errores se quedan hasta que la persona los cierra.
    if (!aviso.hasAttribute('data-fijo')) setTimeout(() => cerrarAviso(aviso), 5000);
  });

  // ---- 3. Diálogo de confirmación -----------------------------------------
  // <form data-confirmar="¿Eliminar el JBL Flip 5?" data-confirmar-boton="Sí, eliminar">
  const dialogo = document.getElementById('confirmar');
  let formularioPendiente = null;

  if (dialogo && typeof dialogo.showModal === 'function') {
    const aceptar = dialogo.querySelector('[data-dialogo-aceptar]');

    document.addEventListener('submit', (evento) => {
      const formulario = evento.target;
      if (!formulario.dataset.confirmar || formulario.dataset.confirmado) return;

      evento.preventDefault();
      formularioPendiente = formulario;
      dialogo.querySelector('#confirmar-texto').textContent = formulario.dataset.confirmar;
      aceptar.textContent = formulario.dataset.confirmarBoton || 'Sí, continuar';
      dialogo.showModal();
    });

    aceptar.addEventListener('click', () => {
      if (!formularioPendiente) return;
      formularioPendiente.dataset.confirmado = '1';
      dialogo.close();
      formularioPendiente.requestSubmit();
    });

    dialogo.querySelector('[data-dialogo-cancelar]').addEventListener('click', () => dialogo.close());
    dialogo.addEventListener('close', () => { formularioPendiente = null; });
    // Clic fuera de la caja: cancelar.
    dialogo.addEventListener('click', (e) => { if (e.target === dialogo) dialogo.close(); });
  }

  // Al enviar un formulario, su botón se desactiva para evitar el doble clic.
  document.addEventListener('submit', (evento) => {
    if (evento.defaultPrevented) return;
    evento.target.querySelectorAll('button[type="submit"]').forEach((boton) => {
      setTimeout(() => { boton.disabled = true; }, 0);
    });
  });

  // ---- 4. Vista previa de la foto -----------------------------------------
  // <input type="file" data-vista="#id-de-la-caja">
  document.querySelectorAll('input[type="file"][data-vista]').forEach((entrada) => {
    entrada.addEventListener('change', () => {
      const caja = document.querySelector(entrada.dataset.vista);
      const archivo = entrada.files[0];
      if (!caja || !archivo) return;

      const imagen = document.createElement('img');
      imagen.alt = 'Vista previa de la foto elegida';
      imagen.src = URL.createObjectURL(archivo);
      imagen.onload = () => URL.revokeObjectURL(imagen.src);
      caja.replaceChildren(imagen);
    });
  });

  // ---- 5. Menú del usuario ------------------------------------------------
  // Se cierra al hacer clic fuera o al presionar Escape.
  const usuario = document.querySelector('details.usuario');
  if (usuario) {
    document.addEventListener('click', (e) => { if (!usuario.contains(e.target)) usuario.open = false; });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') usuario.open = false; });
  }

  // ---- 6. Gráficos --------------------------------------------------------
  // <canvas data-grafico='{"tipo":"barras","etiquetas":[...],"valores":[...],"prefijo":"S/ "}'>
  if (window.Chart) {
    const NARANJA = '#ff6a00';
    const NEGRO = '#17171a';
    const PALETA = [NARANJA, NEGRO, '#a1a1aa', '#fdba74', '#52525b', '#fed7aa'];

    Chart.defaults.font.family = '"Source Sans 3", "Segoe UI", Arial, sans-serif';
    Chart.defaults.font.size = 13;
    Chart.defaults.color = '#6f6f78';
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) Chart.defaults.animation = false;

    document.querySelectorAll('canvas[data-grafico]').forEach((lienzo) => {
      const d = JSON.parse(lienzo.dataset.grafico);
      const prefijo = d.prefijo || '';
      const numero = (v) => prefijo + Number(v).toLocaleString('es-PE', prefijo ? { minimumFractionDigits: 2 } : {});
      const circular = d.tipo === 'dona';

      new Chart(lienzo, {
        type: circular ? 'doughnut' : (d.tipo === 'linea' ? 'line' : 'bar'),
        data: {
          labels: d.etiquetas,
          datasets: [{
            label: d.titulo || '',
            data: d.valores,
            backgroundColor: circular ? PALETA : (d.tipo === 'linea' ? 'rgba(255, 106, 0, .12)' : NARANJA),
            borderColor: circular ? '#fff' : NARANJA,
            borderWidth: circular ? 3 : (d.tipo === 'linea' ? 3 : 0),
            borderRadius: circular ? 0 : 6,
            maxBarThickness: 34,
            fill: d.tipo === 'linea',
            tension: .35,
            pointRadius: 3,
            pointBackgroundColor: NARANJA,
          }],
        },
        options: {
          maintainAspectRatio: false,
          indexAxis: d.horizontal ? 'y' : 'x',
          cutout: circular ? '64%' : undefined,
          plugins: {
            legend: { display: circular, position: 'bottom', labels: { usePointStyle: true, padding: 16 } },
            tooltip: {
              backgroundColor: NEGRO, padding: 10, cornerRadius: 8, displayColors: false,
              callbacks: { label: (c) => `${circular ? c.label + ': ' : ''}${numero(c.parsed.y ?? c.parsed.x ?? c.parsed)}` },
            },
          },
          scales: circular ? {} : {
            x: { grid: { display: !!d.horizontal, color: '#efede8' }, border: { display: false }, ticks: d.horizontal ? { callback: (v) => numero(v) } : {} },
            y: { grid: { display: !d.horizontal, color: '#efede8' }, border: { display: false }, beginAtZero: true, ticks: d.horizontal ? {} : { callback: (v) => numero(v), maxTicksLimit: 6 } },
          },
        },
      });
    });
  }
})();
