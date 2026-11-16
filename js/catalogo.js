/* =========================================================================
   JBL Store — Catálogo
   Filtra y ordena los productos en el navegador. El estado de los filtros
   vive en la dirección (catalogo.html?categoria=audifonos&oferta=1), así
   un filtro se puede compartir y el botón Atrás funciona.
   ========================================================================= */

(() => {
  const { escapar, dinero, tarjeta, icono } = JBL;

  const formulario = document.getElementById('filtros');
  const resultados = document.querySelector('[data-resultados]');
  const vacio = document.querySelector('[data-vacio]');
  const cuenta = document.querySelector('[data-cuenta]');
  const chips = document.querySelector('[data-chips]');
  const precio = document.getElementById('precio');
  const precioTexto = document.querySelector('[data-precio-texto]');
  const orden = document.getElementById('orden');

  let productos = [];
  let categorias = [];
  let precioTope = 0;

  // ---- Estado: se lee de la dirección y se escribe en ella ----------------
  const leerEstado = () => {
    const p = new URLSearchParams(location.search);
    return {
      q: (p.get('q') || '').trim(),
      categoria: p.get('categoria') || '',
      oferta: p.get('oferta') === '1',
      stock: p.get('stock') === '1',
      resistencia: p.get('resistencia') === '1',
      precio: Number(p.get('precio')) || 0,
      orden: p.get('orden') || '',
    };
  };

  const escribirEstado = (estado) => {
    const p = new URLSearchParams();
    if (estado.q) p.set('q', estado.q);
    if (estado.categoria) p.set('categoria', estado.categoria);
    if (estado.oferta) p.set('oferta', '1');
    if (estado.stock) p.set('stock', '1');
    if (estado.resistencia) p.set('resistencia', '1');
    if (estado.precio && estado.precio < precioTope) p.set('precio', estado.precio);
    if (estado.orden) p.set('orden', estado.orden);

    const consulta = p.toString();
    history.replaceState(null, '', consulta ? `?${consulta}` : location.pathname);
  };

  // ---- Filtrado -----------------------------------------------------------
  const sinTildes = (texto) => String(texto || '').toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');

  const coincide = (p, estado, ignorar = '') => {
    if (ignorar !== 'categoria' && estado.categoria && p.categoria !== estado.categoria) return false;
    if (estado.oferta && !p.precio_oferta) return false;
    if (estado.stock && p.stock === 0) return false;
    if (estado.resistencia && !p.resistencia) return false;
    if (estado.precio && p.precio_final > estado.precio) return false;
    if (estado.q) {
      const texto = sinTildes([p.nombre, p.tipo, p.categoria_nombre, p.color, p.sku].filter(Boolean).join(' '));
      if (!sinTildes(estado.q).split(/\s+/).every((palabra) => texto.includes(palabra))) return false;
    }
    return true;
  };

  const ordenar = (lista, criterio) => {
    const copia = [...lista];
    if (criterio === 'precio-asc') copia.sort((a, b) => a.precio_final - b.precio_final);
    if (criterio === 'precio-desc') copia.sort((a, b) => b.precio_final - a.precio_final);
    if (criterio === 'descuento') copia.sort((a, b) => b.descuento - a.descuento);
    if (criterio === 'nombre') copia.sort((a, b) => a.nombre.localeCompare(b.nombre, 'es'));
    return copia;
  };

  // ---- Pintado ------------------------------------------------------------
  const pintar = () => {
    const estado = leerEstado();

    // Un enlace viejo puede traer una categoría que ya no existe: se ignora.
    if (estado.categoria && !categorias.some((c) => c.slug === estado.categoria)) {
      estado.categoria = '';
      escribirEstado(estado);
    }
    const lista = ordenar(productos.filter((p) => coincide(p, estado)), estado.orden);

    resultados.innerHTML = lista.map(tarjeta).join('');
    resultados.hidden = lista.length === 0;
    vacio.hidden = lista.length !== 0;
    cuenta.textContent = lista.length === 1 ? '1 producto' : `${lista.length} productos`;

    // Título según la categoría elegida o la búsqueda.
    const categoria = categorias.find((c) => c.slug === estado.categoria);
    const titulo = document.querySelector('[data-titulo]');
    const bajada = document.querySelector('[data-bajada]');
    if (estado.q) {
      titulo.textContent = `“${estado.q}”`;
      bajada.textContent = 'Resultados de tu búsqueda.';
    } else if (categoria) {
      titulo.textContent = categoria.nombre;
      bajada.textContent = categoria.descripcion || '';
    } else {
      titulo.textContent = estado.oferta ? 'Ofertas' : 'Catálogo';
      bajada.textContent = estado.oferta ? 'Productos con descuento por tiempo limitado.' : 'Todos los productos de la tienda.';
    }
    document.title = `${titulo.textContent} — JBL Store`;

    // Contadores: cuántos productos quedarían con cada opción.
    formulario.querySelectorAll('[data-cuenta-categoria]').forEach((el) => {
      const slug = el.dataset.cuentaCategoria;
      el.textContent = productos.filter((p) => coincide(p, estado, 'categoria') && (!slug || p.categoria === slug)).length;
    });
    const contar = (clave, prueba) => {
      const el = formulario.querySelector(`[data-cuenta-${clave}]`);
      if (el) el.textContent = productos.filter((p) => coincide(p, { ...estado, [clave]: false }) && prueba(p)).length;
    };
    contar('oferta', (p) => p.precio_oferta);
    contar('stock', (p) => p.stock > 0);
    contar('resistencia', (p) => p.resistencia);

    // Chips: un botón por cada filtro activo, para quitarlo de un clic.
    const activos = [];
    if (estado.q) activos.push(['q', `Búsqueda: ${estado.q}`]);
    if (categoria) activos.push(['categoria', categoria.nombre]);
    if (estado.oferta) activos.push(['oferta', 'En oferta']);
    if (estado.stock) activos.push(['stock', 'Con stock']);
    if (estado.resistencia) activos.push(['resistencia', 'Resistentes al agua']);
    if (estado.precio && estado.precio < precioTope) activos.push(['precio', `Hasta ${dinero(estado.precio, true)}`]);
    chips.innerHTML = activos.map(([clave, texto]) =>
      `<button class="chip" type="button" data-quitar="${clave}" aria-label="Quitar el filtro ${escapar(texto)}">${escapar(texto)} ${icono('x')}</button>`).join('');

    JBL.observarApariciones(resultados);
  };

  /** Pone los controles del formulario según el estado de la dirección. */
  const reflejarEnFormulario = () => {
    const estado = leerEstado();
    formulario.querySelectorAll('[name="categoria"]').forEach((radio) => { radio.checked = radio.value === estado.categoria; });
    formulario.elements.oferta.checked = estado.oferta;
    formulario.elements.stock.checked = estado.stock;
    formulario.elements.resistencia.checked = estado.resistencia;
    precio.value = estado.precio || precioTope;
    precioTexto.textContent = `Hasta ${dinero(Number(precio.value), true)}`;
    orden.value = estado.orden;
  };

  const alCambiar = () => {
    const datos = new FormData(formulario);
    escribirEstado({
      q: leerEstado().q,
      categoria: datos.get('categoria') || '',
      oferta: datos.has('oferta'),
      stock: datos.has('stock'),
      resistencia: datos.has('resistencia'),
      precio: Number(precio.value),
      orden: orden.value,
    });
    precioTexto.textContent = `Hasta ${dinero(Number(precio.value), true)}`;
    pintar();
  };

  // ---- Eventos ------------------------------------------------------------
  formulario.addEventListener('input', alCambiar);
  orden.addEventListener('change', alCambiar);

  formulario.addEventListener('reset', () => {
    history.replaceState(null, '', location.pathname);
    const buscador = document.getElementById('buscar');
    if (buscador) buscador.value = '';
    // El navegador restablece los campos después de este evento.
    setTimeout(() => { reflejarEnFormulario(); pintar(); }, 0);
  });

  chips.addEventListener('click', (evento) => {
    const boton = evento.target.closest('[data-quitar]');
    if (!boton) return;

    const estado = leerEstado();
    const clave = boton.dataset.quitar;
    estado[clave] = typeof estado[clave] === 'boolean' ? false : (clave === 'precio' ? 0 : '');
    escribirEstado(estado);
    if (clave === 'q') document.getElementById('buscar').value = '';
    reflejarEnFormulario();
    pintar();
  });

  document.querySelector('[data-filtros-abrir]').addEventListener('click', () => {
    document.body.classList.add('filtros-abiertos', 'sin-scroll');
  });

  // ---- Arranque -----------------------------------------------------------
  JBL.alCargar((catalogo) => {
    productos = catalogo.productos;
    categorias = catalogo.categorias;

    // El tope del control de precio es el producto más caro, redondeado a 50.
    precioTope = Math.ceil(Math.max(...productos.map((p) => p.precio_final), 50) / 50) * 50;
    precio.max = precioTope;
    precio.min = productos.length ? Math.floor(Math.min(...productos.map((p) => p.precio_final)) / 10) * 10 : 0;

    document.querySelector('[data-filtro-categorias]').innerHTML = [{ slug: '', nombre: 'Todas' }, ...categorias].map((c) => `
      <label class="opcion">
        <input type="radio" name="categoria" value="${escapar(c.slug)}">
        <span>${escapar(c.nombre)}</span>
        <span data-cuenta-categoria="${escapar(c.slug)}"></span>
      </label>`).join('');

    reflejarEnFormulario();
    pintar();
  });
})();
