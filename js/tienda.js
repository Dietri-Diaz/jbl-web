/* =========================================================================
   JBL Store — Base de la tienda
   Lo comparten todas las páginas. Expone un único objeto global, JBL, con:

     1. Catálogo   de dónde salen los productos (ERP o copia estática)
     2. Utilidades dinero, escapar texto, leer la dirección
     3. Carrito    guardado en el navegador (localStorage)
     4. Plantillas tarjeta de producto, cabecera, pie y cajón del carrito
     5. Avisos     mensajes breves en la parte inferior
     6. Arranque   menú móvil, cajón y aparición al hacer scroll
   ========================================================================= */

window.JBL = (() => {
  'use strict';

  // ---- 1. Catálogo -----------------------------------------------------
  // La copia estática (data/catalogo.js) hace que la tienda funcione sin
  // servidor: en Vercel o abriendo el archivo con doble clic. Si el ERP
  // responde, sus datos reemplazan a la copia: precios y stock al día.

  // En la nube no hay ERP en 127.0.0.1: solo se busca ahí en la propia PC.
  const ES_LOCAL = ['localhost', '127.0.0.1', ''].includes(location.hostname);
  const ERP_URL = document.documentElement.dataset.erp || (ES_LOCAL ? 'http://127.0.0.1:8000' : '');
  const ESPERA_MAXIMA_MS = 2500;

  let catalogo = window.JBL_CATALOGO || { tienda: {}, categorias: [], productos: [] };
  let origen = 'copia';          // 'copia' o 'erp'
  const oyentes = [];

  /** Ejecuta fn con el catálogo ahora, y otra vez si llegan datos del ERP. */
  const alCargar = (fn) => {
    oyentes.push(fn);
    fn(catalogo, origen);
  };

  const sincronizarConErp = async () => {
    if (!ERP_URL) return;

    const control = new AbortController();
    const reloj = setTimeout(() => control.abort(), ESPERA_MAXIMA_MS);

    try {
      const respuesta = await fetch(`${ERP_URL}/api/catalogo`, { headers: { Accept: 'application/json' }, signal: control.signal });
      if (!respuesta.ok) return;

      const datos = await respuesta.json();
      if (!Array.isArray(datos.productos) || !Array.isArray(datos.categorias)) return;

      catalogo = datos;
      origen = 'erp';
      oyentes.forEach((fn) => fn(catalogo, origen));
      document.dispatchEvent(new CustomEvent('jbl:carrito'));
    } catch {
      // El ERP no está encendido: la tienda sigue con la copia estática.
    } finally {
      clearTimeout(reloj);
    }
  };

  const reglas = () => ({
    moneda: 'S/', envio: 12, envio_gratis_desde: 299, maximo_por_producto: 5,
    entregas: {}, pagos: {}, estados: {},
    ...catalogo.tienda,
  });

  const producto = (slug) => catalogo.productos.find((p) => p.slug === slug);
  const productoPorId = (id) => catalogo.productos.find((p) => p.id === Number(id));

  // ---- 2. Utilidades ---------------------------------------------------
  /** 1299.5 -> "S/ 1,299.50". Con corto=true omite los céntimos si son ,00. */
  const dinero = (monto, corto = false) => {
    const entero = Number.isInteger(Number(monto));
    const texto = Number(monto).toLocaleString('es-PE', {
      minimumFractionDigits: corto && entero ? 0 : 2,
      maximumFractionDigits: 2,
    });
    return `${reglas().moneda} ${texto}`;
  };

  // Los datos vienen de un formulario del panel: se escapan antes de ir al HTML.
  const escapar = (texto) => String(texto ?? '').replace(/[&<>"']/g, (c) => (
    { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]
  ));

  const parametro = (nombre) => new URLSearchParams(location.search).get(nombre) || '';

  /** "20 W · 12 h · IPX7", solo con los datos que el producto tiene. */
  const ficha = (p) => [
    p.potencia_w ? `${p.potencia_w} W` : '',
    p.bateria_h ? `${p.bateria_h} h` : '',
    p.resistencia || '',
  ].filter(Boolean).join(' · ') || p.tipo || '';

  const icono = (nombre, clase = '') => `<svg class="ico ${clase}" aria-hidden="true"><use href="#i-${nombre}"/></svg>`;

  // ---- 3. Carrito ------------------------------------------------------
  // Se guarda solo el id y la cantidad. El nombre y el precio se leen
  // siempre del catálogo, así un cambio de precio se refleja al instante.
  const CLAVE = 'jbl-carrito';

  const leer = () => {
    try {
      const guardado = JSON.parse(localStorage.getItem(CLAVE)) || [];
      return guardado.filter((l) => Number.isInteger(l.id) && Number.isInteger(l.cantidad) && l.cantidad > 0);
    } catch {
      return [];
    }
  };

  const guardar = (lineas) => {
    try { localStorage.setItem(CLAVE, JSON.stringify(lineas)); } catch { /* modo privado: no se guarda */ }
    document.dispatchEvent(new CustomEvent('jbl:carrito'));
  };

  /** Cuántas unidades de un producto se pueden pedir como máximo. */
  const tope = (p) => Math.min(p.stock, reglas().maximo_por_producto);

  const carrito = {
    /** Líneas listas para mostrar: producto, cantidad y subtotal. */
    lineas() {
      return leer()
        .map((l) => ({ producto: productoPorId(l.id), cantidad: l.cantidad }))
        .filter((l) => l.producto)
        .map((l) => ({ ...l, subtotal: l.producto.precio_final * l.cantidad, excede: l.cantidad > l.producto.stock }));
    },

    unidades() {
      return this.lineas().reduce((suma, l) => suma + l.cantidad, 0);
    },

    /** Subtotal, envío y total. El envío depende del tipo de entrega. */
    totales(entrega = 'delivery') {
      const r = reglas();
      const subtotal = this.lineas().reduce((suma, l) => suma + l.subtotal, 0);
      const envio = entrega === 'delivery' && subtotal > 0 && subtotal < r.envio_gratis_desde ? r.envio : 0;
      return { subtotal, envio, total: subtotal + envio, falta: Math.max(0, r.envio_gratis_desde - subtotal) };
    },

    /** Agrega unidades. Devuelve cuántas se agregaron de verdad (0 si no hay stock). */
    agregar(id, cantidad = 1) {
      const p = productoPorId(id);
      if (!p) return 0;

      const lineas = leer();
      const linea = lineas.find((l) => l.id === p.id);
      const actual = linea ? linea.cantidad : 0;
      const nueva = Math.min(actual + cantidad, tope(p));
      if (nueva <= actual) return 0;

      if (linea) linea.cantidad = nueva; else lineas.push({ id: p.id, cantidad: nueva });
      guardar(lineas);
      return nueva - actual;
    },

    cambiar(id, cantidad) {
      const p = productoPorId(id);
      const lineas = leer();
      const linea = lineas.find((l) => l.id === Number(id));
      if (!p || !linea) return;

      linea.cantidad = Math.max(1, Math.min(cantidad, Math.max(1, tope(p))));
      guardar(lineas);
    },

    quitar(id) {
      guardar(leer().filter((l) => l.id !== Number(id)));
    },

    vaciar() {
      guardar([]);
    },
  };

  // ---- 4. Plantillas ---------------------------------------------------
  const precio = (p) => `
    <p class="precio">
      ${p.precio_oferta ? `<span class="precio__antes"><span class="sr-only">Antes </span>${dinero(p.precio, true)}</span>` : ''}
      <span class="precio__ahora">${dinero(p.precio_final, true)}</span>
    </p>`;

  const sellos = (p) => `
    <div class="prod__sellos">
      ${p.stock === 0 ? '<span class="sello sello--agotado">Agotado</span>' : ''}
      ${p.descuento ? `<span class="sello sello--oferta">-${p.descuento}%</span>` : ''}
      ${p.sello ? `<span class="sello">${escapar(p.sello)}</span>` : ''}
    </div>`;

  /** Tarjeta de producto para las rejillas del inicio y del catálogo. */
  const tarjeta = (p) => `
    <article class="prod">
      <div class="prod__foto">
        ${p.foto ? `<img src="${escapar(p.foto)}" alt="${escapar(p.foto_alt)}" loading="lazy" width="600" height="600">` : ''}
        ${sellos(p)}
      </div>
      <div class="prod__cuerpo">
        <p class="prod__cat">${escapar(p.categoria_nombre)}</p>
        <h3 class="prod__nombre"><a href="producto.html?p=${encodeURIComponent(p.slug)}">${escapar(p.nombre)}</a></h3>
        <p class="prod__specs">${escapar(ficha(p))}</p>
        <div class="prod__pie">
          ${precio(p)}
          <button class="prod__agregar" type="button" data-agregar="${p.id}" ${p.stock === 0 ? 'disabled' : ''}
                  aria-label="${p.stock === 0 ? `${escapar(p.nombre)} agotado` : `Agregar ${escapar(p.nombre)} al carrito`}">
            ${icono('mas')}
          </button>
        </div>
      </div>
    </article>`;

  const SPRITE = `
    <svg class="sprite" aria-hidden="true" focusable="false">
      <symbol id="i-mas" viewBox="0 0 24 24"><path d="M5 12h14"/><path d="M12 5v14"/></symbol>
      <symbol id="i-menos" viewBox="0 0 24 24"><path d="M5 12h14"/></symbol>
      <symbol id="i-x" viewBox="0 0 24 24"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></symbol>
      <symbol id="i-flecha" viewBox="0 0 24 24"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></symbol>
      <symbol id="i-buscar" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></symbol>
      <symbol id="i-bolsa" viewBox="0 0 24 24"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></symbol>
      <symbol id="i-menu" viewBox="0 0 24 24"><path d="M4 6h16"/><path d="M4 12h16"/><path d="M4 18h16"/></symbol>
      <symbol id="i-check" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></symbol>
      <symbol id="i-alerta" viewBox="0 0 24 24"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4"/><path d="M12 17h.01"/></symbol>
      <symbol id="i-filtros" viewBox="0 0 24 24"><path d="M3 6h18"/><path d="M7 12h10"/><path d="M10 18h4"/></symbol>
      <symbol id="i-camion" viewBox="0 0 24 24"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></symbol>
      <symbol id="i-tienda" viewBox="0 0 24 24"><path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/><path d="M2 7h20"/><path d="M22 7v3a2 2 0 0 1-2 2a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 16 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 12 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 8 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 4 12a2 2 0 0 1-2-2V7"/></symbol>
      <symbol id="i-billete" viewBox="0 0 24 24"><rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/></symbol>
      <symbol id="i-sonido" viewBox="0 0 24 24"><path d="M11 5 6 9H2v6h4l5 4V5Z"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/></symbol>
      <symbol id="i-agua" viewBox="0 0 24 24"><path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5s-3.5-4-4-6.5c-.5 2.5-2 4.9-4 6.5S5 13 5 15a7 7 0 0 0 7 7Z"/></symbol>
      <symbol id="i-bateria" viewBox="0 0 24 24"><rect x="2" y="7" width="16" height="10" rx="2"/><path d="M22 11v2"/><path d="M6 11v2"/><path d="M10 11v2"/></symbol>
      <symbol id="i-enlace" viewBox="0 0 24 24"><path d="M4.93 19.07a10 10 0 0 1 0-14.14"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/><path d="M7.76 16.24a6 6 0 0 1 0-8.49"/><path d="M16.24 7.76a6 6 0 0 1 0 8.49"/><circle cx="12" cy="12" r="2"/></symbol>
      <symbol id="i-correo" viewBox="0 0 24 24"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></symbol>
      <symbol id="i-reloj" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></symbol>
      <symbol id="i-pin" viewBox="0 0 24 24"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></symbol>
    </svg>`;

  const PAGINAS = [
    ['index.html', 'Inicio'],
    ['catalogo.html', 'Catálogo'],
    ['catalogo.html?oferta=1', 'Ofertas'],
    ['pedido.html', 'Mi pedido'],
    ['index.html#contacto', 'Contacto'],
  ];

  /** Escribe la cabecera justo antes del <script> que la llama. */
  const cabecera = () => {
    const actual = (location.pathname.split('/').pop() || 'index.html') + location.search;
    const enlaces = PAGINAS.map(([url, texto]) =>
      `<a href="${url}"${url === actual ? ' aria-current="page"' : ''}>${texto}</a>`).join('');

    document.currentScript.insertAdjacentHTML('beforebegin', `
      ${SPRITE}
      <a class="salto" href="#contenido">Saltar al contenido</a>
      <p class="anuncio" data-anuncio></p>
      <header class="cab">
        <div class="contenedor cab__caja">
          <a class="cab__logo" href="index.html" aria-label="JBL Store, ir al inicio">
            <img src="assets/img/logo-jbl.svg" alt="JBL" width="62" height="44">
          </a>
          <nav class="cab__menu" id="menu" aria-label="Navegación principal">${enlaces}</nav>
          <form class="cab__buscar" action="catalogo.html" role="search">
            ${icono('buscar')}
            <label class="sr-only" for="buscar">Buscar productos</label>
            <input id="buscar" name="q" type="search" placeholder="Buscar productos" autocomplete="off">
          </form>
          <button class="cab__icono" type="button" data-carrito-abrir aria-label="Abrir el carrito" aria-haspopup="dialog">
            ${icono('bolsa')}
            <span class="cab__cuenta" data-carrito-cuenta hidden>0</span>
          </button>
          <button class="cab__icono cab__hamb" type="button" data-menu aria-controls="menu" aria-expanded="false" aria-label="Abrir el menú">
            ${icono('menu')}
          </button>
        </div>
      </header>`);
  };

  /** Escribe el pie, el cajón del carrito y la zona de avisos. */
  const pie = () => {
    document.currentScript.insertAdjacentHTML('beforebegin', `
      <footer class="pie">
        <div class="contenedor">
          <div class="pie__rejilla">
            <div>
              <img src="assets/img/logo-jbl.svg" alt="JBL" width="88" height="62">
              <p class="pie__resumen">
                James B. Lansing fundó la marca en 1946 fabricando altavoces para salas de cine.
                Hoy JBL es líder mundial en audio portátil, con más de 100 millones de parlantes vendidos.
              </p>
            </div>
            <nav class="pie__columna" aria-label="Categorías">
              <h2 class="pie__titulo">Categorías</h2>
              <ul data-pie-categorias></ul>
            </nav>
            <nav class="pie__columna" aria-label="La tienda">
              <h2 class="pie__titulo">La tienda</h2>
              <ul>
                <li><a href="catalogo.html">Catálogo completo</a></li>
                <li><a href="catalogo.html?oferta=1">Ofertas</a></li>
                <li><a href="carrito.html">Carrito</a></li>
                <li><a href="pedido.html">Seguir mi pedido</a></li>
                <li><a href="index.html#contacto">Contacto</a></li>
              </ul>
            </nav>
            <nav class="pie__columna" aria-label="Enlaces oficiales">
              <h2 class="pie__titulo">Oficial</h2>
              <ul>
                <li><a href="https://www.jbl.com/" target="_blank" rel="noopener noreferrer">Sitio global de JBL</a></li>
                <li><a href="https://www.jbl.com.pe/" target="_blank" rel="noopener noreferrer">JBL Perú</a></li>
                <li><a href="https://www.harman.com/" target="_blank" rel="noopener noreferrer">Harman International</a></li>
                <li><a href="https://github.com/Dietri-Diaz/jbl-web/blob/main/docs/creditos-imagenes.md" target="_blank" rel="noopener noreferrer">Créditos de imágenes</a></li>
              </ul>
            </nav>
          </div>
          <div class="pie__barra">
            <p class="pie__aviso">
              <strong>Tienda de demostración, proyecto académico sin fines comerciales.</strong>
              Este sitio no es oficial ni está afiliado a JBL: no se cobra ni se envía ningún producto, y los
              precios y el stock son datos de ejemplo. La marca JBL, su logotipo y los nombres de los productos son
              propiedad de Harman International Industries, Inc., y se reproducen únicamente con fines educativos.
              Las fotografías proceden de Wikimedia Commons bajo licencias libres.
            </p>
            <p>Curso de Herramientas de Desarrollo · UTP · 2026</p>
          </div>
        </div>
      </footer>

      <button class="velo" type="button" data-cerrar-todo aria-label="Cerrar" tabindex="-1"></button>
      <aside class="cajon" id="cajon" role="dialog" aria-modal="true" aria-labelledby="cajon-titulo" inert>
        <div class="cajon__cab">
          <h2 class="cajon__titulo" id="cajon-titulo">Tu carrito</h2>
          <button class="cajon__cerrar" type="button" data-cerrar-todo aria-label="Cerrar el carrito">${icono('x')}</button>
        </div>
        <div class="cajon__lista" data-cajon-lista></div>
        <div class="cajon__pie" data-cajon-pie></div>
      </aside>
      <div class="avisos" aria-live="polite" data-avisos></div>`);

    arrancar();
  };

  // ---- 5. Avisos -------------------------------------------------------
  const avisar = (texto, { tipo = 'ok', enlace = '', textoEnlace = '' } = {}) => {
    const zona = document.querySelector('[data-avisos]');
    if (!zona) return;

    const aviso = document.createElement('div');
    aviso.className = `aviso aviso--${tipo}`;
    aviso.setAttribute('role', tipo === 'error' ? 'alert' : 'status');
    aviso.innerHTML = `${icono(tipo === 'error' ? 'alerta' : 'check')}<span>${escapar(texto)}</span>${
      enlace ? `<a href="${enlace}">${escapar(textoEnlace)}</a>` : ''}`;
    zona.append(aviso);

    setTimeout(() => {
      aviso.classList.add('saliendo');
      setTimeout(() => aviso.remove(), 220);
    }, 3200);
  };

  // ---- 6. Arranque -----------------------------------------------------
  const pintarCajon = () => {
    const lista = document.querySelector('[data-cajon-lista]');
    const zonaPie = document.querySelector('[data-cajon-pie]');
    const cuenta = document.querySelector('[data-carrito-cuenta]');
    if (!lista) return;

    const lineas = carrito.lineas();
    const unidades = carrito.unidades();

    if (cuenta) {
      if (cuenta.textContent !== String(unidades) && unidades > 0) {
        cuenta.classList.remove('salta');
        void cuenta.offsetWidth;          // reinicia la animación
        cuenta.classList.add('salta');
      }
      cuenta.textContent = unidades;
      cuenta.hidden = unidades === 0;
    }

    if (lineas.length === 0) {
      lista.innerHTML = `
        <div class="cajon__vacio">
          <p><b>Tu carrito está vacío.</b><br>Agrega un producto para empezar.</p>
          <a class="boton boton--oscuro" href="catalogo.html">Ver el catálogo</a>
        </div>`;
      zonaPie.innerHTML = '';
      return;
    }

    lista.innerHTML = lineas.map((l) => `
      <div class="mini">
        ${l.producto.foto ? `<img src="${escapar(l.producto.foto)}" alt="" width="56" height="56" loading="lazy">` : '<span></span>'}
        <div>
          <b>${escapar(l.producto.nombre)}</b>
          <span>${l.cantidad} × ${dinero(l.producto.precio_final, true)}</span>
        </div>
        <strong>${dinero(l.subtotal, true)}</strong>
      </div>`).join('');

    const { subtotal, falta } = carrito.totales();
    zonaPie.innerHTML = `
      <p class="cajon__total"><span>Subtotal</span><strong>${dinero(subtotal)}</strong></p>
      <p class="resumen__nota" style="margin:0;text-align:left">${falta > 0
        ? `Te faltan ${dinero(falta)} para el envío gratis.`
        : 'Tu pedido tiene envío gratis.'}</p>
      <a class="boton boton--solido boton--bloque" href="checkout.html">Finalizar compra</a>
      <a class="boton boton--linea boton--bloque" href="carrito.html">Ver el carrito</a>`;
  };

  const arrancar = () => {
    const cajon = document.getElementById('cajon');
    const menu = document.getElementById('menu');
    const botonMenu = document.querySelector('[data-menu]');
    let volverA = null;

    // Lo que queda detrás del cajón: no debe recibir el foco mientras esté abierto.
    const fondo = () => document.querySelectorAll('.cab, main, .pie, .anuncio');

    const abrirCajon = () => {
      volverA = document.activeElement;
      pintarCajon();
      cajon.inert = false;
      fondo().forEach((el) => { el.inert = true; });
      document.body.classList.add('cajon-abierto', 'sin-scroll');
      cajon.querySelector('.cajon__cerrar').focus();
    };

    const cerrarTodo = () => {
      const estabaAbierto = document.body.classList.contains('cajon-abierto');
      document.body.classList.remove('cajon-abierto', 'filtros-abiertos', 'sin-scroll');
      cajon.inert = true;
      fondo().forEach((el) => { el.inert = false; });
      menu.classList.remove('abierto');
      botonMenu.setAttribute('aria-expanded', 'false');
      if (estabaAbierto && volverA) volverA.focus();
    };

    document.addEventListener('click', (evento) => {
      const objetivo = evento.target;

      if (objetivo.closest('[data-carrito-abrir]')) return abrirCajon();
      if (objetivo.closest('[data-cerrar-todo]')) return cerrarTodo();

      if (objetivo.closest('[data-menu]')) {
        const abierto = menu.classList.toggle('abierto');
        botonMenu.setAttribute('aria-expanded', String(abierto));
        return;
      }

      // Botón "+" de cualquier tarjeta de producto.
      const agregar = objetivo.closest('[data-agregar]');
      if (agregar) {
        const p = productoPorId(agregar.dataset.agregar);
        const agregadas = carrito.agregar(agregar.dataset.agregar, 1);
        if (agregadas > 0) {
          avisar(`${p.nombre} está en tu carrito.`, { enlace: 'carrito.html', textoEnlace: 'Ver carrito' });
        } else {
          avisar(`Ya tienes el máximo disponible de ${p.nombre}.`, { tipo: 'error' });
        }
      }
    });

    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') cerrarTodo(); });
    document.addEventListener('jbl:carrito', pintarCajon);
    // Si el carrito cambia en otra pestaña, esta se pone al día.
    window.addEventListener('storage', (e) => { if (e.key === CLAVE) document.dispatchEvent(new CustomEvent('jbl:carrito')); });

    alCargar((datos) => {
      const r = reglas();
      const anuncio = document.querySelector('[data-anuncio]');
      if (anuncio) anuncio.textContent = `Envío gratis en Lima desde ${dinero(r.envio_gratis_desde, true)} · Pago contra entrega · Recojo en tienda`;

      const lista = document.querySelector('[data-pie-categorias]');
      if (lista) {
        lista.innerHTML = datos.categorias.map((c) =>
          `<li><a href="catalogo.html?categoria=${encodeURIComponent(c.slug)}">${escapar(c.nombre)}</a></li>`).join('');
      }
      pintarCajon();
    });

    // El buscador conserva lo que se buscó.
    const buscar = document.getElementById('buscar');
    if (buscar) buscar.value = parametro('q');

    observarApariciones();
    sincronizarConErp();
  };

  /** Los bloques con clase "reveal" aparecen al entrar en pantalla. */
  const observarApariciones = (raiz = document) => {
    const elementos = raiz.querySelectorAll('.reveal:not(.visible)');
    if (!('IntersectionObserver' in window)) return elementos.forEach((el) => el.classList.add('visible'));

    const observador = new IntersectionObserver((entradas) => {
      entradas.forEach((entrada) => {
        if (!entrada.isIntersecting) return;
        entrada.target.classList.add('visible');
        observador.unobserve(entrada.target);
      });
    }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });

    elementos.forEach((el) => observador.observe(el));
  };

  return {
    ERP_URL,
    get catalogo() { return catalogo; },
    get origen() { return origen; },
    alCargar, reglas, producto, productoPorId,
    dinero, escapar, parametro, ficha, icono,
    carrito, tope,
    tarjeta, precio, sellos, cabecera, pie, avisar, observarApariciones,
  };
})();
