/**
 * catalogo.js — Lógica del Catálogo de Productos JBL
 * Rama: feature/catalogo-productos
 *
 * Funciones principales:
 *  - cargarProductos()     : Carga el JSON con fetch
 *  - renderizarGrid()      : Genera las tarjetas en el DOM
 *  - filtrarYOrdenar()     : Aplica búsqueda, filtro de categoría y ordenamiento
 *  - abrirModal()          : Muestra el modal de detalles
 *  - cerrarModal()         : Oculta el modal
 *  - crearTarjeta()        : Construye el HTML de una tarjeta de producto
 */

'use strict';

/* ═══════════════════════════════════════════════════
   CONSTANTES Y ESTADO DE LA APLICACIÓN
   ═══════════════════════════════════════════════════ */

/** Ruta al archivo JSON de productos */
const RUTA_JSON = 'data/productos.json';

/** Imagen placeholder si la imagen del producto no carga */
const IMAGEN_PLACEHOLDER = 'https://placehold.co/400x300/fff3ea/ff6a00?text=JBL';

/**
 * Estado centralizado del catálogo.
 * Evita variables globales sueltas y facilita la lectura.
 */
const estado = {
  productos: [],          // Todos los productos cargados del JSON
  productosFiltrados: [], // Productos tras aplicar filtros
  categoriaActiva: 'Todos', // Filtro de categoría activo
  textoBusqueda: '',       // Texto actual del buscador
  orden: 'relevancia',    // Orden seleccionado en el selector
};

/* ═══════════════════════════════════════════════════
   REFERENCIAS A ELEMENTOS DEL DOM
   ═══════════════════════════════════════════════════ */

/** Busca un elemento en el DOM; lanza error si no existe. */
function obtenerElemento(id) {
  const el = document.getElementById(id);
  if (!el) console.warn(`[Catálogo] Elemento no encontrado: #${id}`);
  return el;
}

const elems = {
  grid:           obtenerElemento('cat-grid'),
  cargando:       obtenerElemento('cat-cargando'),
  sinResultados:  obtenerElemento('cat-sin-resultados'),
  inputBusqueda:  obtenerElemento('cat-input-busqueda'),
  filtrosBtns:    document.querySelectorAll('.cat-filtros__btn'),
  selectOrden:    obtenerElemento('cat-select-orden'),
  contadorInfo:   obtenerElemento('cat-contador-info'),
  modal:          obtenerElemento('cat-modal'),
  modalImagen:    obtenerElemento('cat-modal-imagen'),
  modalCategoria: obtenerElemento('cat-modal-categoria'),
  modalNombre:    obtenerElemento('cat-modal-nombre'),
  modalPrecio:    obtenerElemento('cat-modal-precio'),
  modalDescripcion:   obtenerElemento('cat-modal-descripcion'),
  modalCaracteristicas: obtenerElemento('cat-modal-caracteristicas'),
  btnCerrarModal: obtenerElemento('cat-btn-cerrar-modal'),
  btnReset:       obtenerElemento('cat-btn-reset'),
};

/* ═══════════════════════════════════════════════════
   CARGA DE DATOS
   ═══════════════════════════════════════════════════ */

/**
 * Carga los productos desde el archivo JSON.
 * Muestra el spinner mientras carga y lo oculta al terminar.
 */
async function cargarProductos() {
  mostrarCargando(true);

  try {
    const respuesta = await fetch(RUTA_JSON);

    if (!respuesta.ok) {
      throw new Error(`Error HTTP ${respuesta.status} al cargar ${RUTA_JSON}`);
    }

    const datos = await respuesta.json();

    // Guardamos todos los productos en el estado
    estado.productos = datos;
    estado.productosFiltrados = [...datos];

    // Renderizamos el catálogo por primera vez
    renderizarGrid(estado.productosFiltrados);

  } catch (error) {
    console.error('[Catálogo] Error al cargar productos:', error);
    mostrarError('No se pudieron cargar los productos. Verifica que el archivo data/productos.json esté disponible.');
  } finally {
    mostrarCargando(false);
  }
}

/* ═══════════════════════════════════════════════════
   FILTRADO Y ORDENAMIENTO
   ═══════════════════════════════════════════════════ */

/**
 * Aplica el filtro de categoría, el buscador y el orden.
 * Llama a renderizarGrid() con el resultado final.
 */
function filtrarYOrdenar() {
  let resultado = [...estado.productos];

  // 1. Filtrar por categoría
  if (estado.categoriaActiva !== 'Todos') {
    resultado = resultado.filter(
      (p) => p.categoria === estado.categoriaActiva
    );
  }

  // 2. Filtrar por texto (nombre, categoría y descripción)
  const texto = estado.textoBusqueda.trim().toLowerCase();
  if (texto !== '') {
    resultado = resultado.filter((p) => {
      const enNombre      = p.nombre.toLowerCase().includes(texto);
      const enCategoria   = p.categoria.toLowerCase().includes(texto);
      const enDescripcion = p.descripcion.toLowerCase().includes(texto);
      return enNombre || enCategoria || enDescripcion;
    });
  }

  // 3. Ordenar
  resultado = ordenarProductos(resultado, estado.orden);

  // Guardamos el resultado filtrado en el estado
  estado.productosFiltrados = resultado;

  // Renderizamos
  renderizarGrid(resultado);
}

/**
 * Ordena un array de productos según el criterio dado.
 * @param {Array} lista - Productos a ordenar
 * @param {string} criterio - Criterio de orden
 * @returns {Array} - Productos ordenados
 */
function ordenarProductos(lista, criterio) {
  const copia = [...lista];

  switch (criterio) {
    case 'precio-asc':
      return copia.sort((a, b) => a.precio - b.precio);

    case 'precio-desc':
      return copia.sort((a, b) => b.precio - a.precio);

    case 'nombre-az':
      return copia.sort((a, b) => a.nombre.localeCompare(b.nombre, 'es'));

    case 'nombre-za':
      return copia.sort((a, b) => b.nombre.localeCompare(a.nombre, 'es'));

    case 'relevancia':
    default:
      // "Relevancia" = orden original del JSON (por ID)
      return copia.sort((a, b) => a.id - b.id);
  }
}

/* ═══════════════════════════════════════════════════
   RENDERIZADO DEL GRID
   ═══════════════════════════════════════════════════ */

/**
 * Genera y muestra las tarjetas de producto en el DOM.
 * @param {Array} lista - Productos a mostrar
 */
function renderizarGrid(lista) {
  // Limpiamos el grid actual
  elems.grid.innerHTML = '';

  // Actualizamos el contador de resultados
  actualizarContador(lista.length);

  if (lista.length === 0) {
    // Sin resultados
    mostrarSinResultados(true);
    return;
  }

  mostrarSinResultados(false);

  // Creamos un fragmento para un mejor rendimiento (un solo reflow)
  const fragmento = document.createDocumentFragment();

  lista.forEach((producto, indice) => {
    const tarjeta = crearTarjeta(producto, indice);
    fragmento.appendChild(tarjeta);
  });

  elems.grid.appendChild(fragmento);
}

/**
 * Construye y devuelve el elemento DOM de una tarjeta de producto.
 * @param {Object} producto - Datos del producto
 * @param {number} indice - Índice para escalonar la animación
 * @returns {HTMLElement}
 */
function crearTarjeta(producto, indice) {
  // Formateamos el precio en soles peruanos
  const precioFormateado = formatearPrecio(producto.precio);

  // Creamos el contenedor de la tarjeta
  const article = document.createElement('article');
  article.className = 'cat-tarjeta';
  article.setAttribute('role', 'listitem');
  article.style.animationDelay = `${indice * 0.04}s`;

  article.innerHTML = `
    <div class="cat-tarjeta__imagen-wrap">
      <img
        class="cat-tarjeta__imagen"
        src="${producto.imagen}"
        alt="${producto.nombre}"
        loading="lazy"
        onerror="this.src='${IMAGEN_PLACEHOLDER}'"
      >
      <span class="cat-tarjeta__badge-cat">${producto.categoria}</span>
    </div>
    <div class="cat-tarjeta__cuerpo">
      <h3 class="cat-tarjeta__nombre">${producto.nombre}</h3>
      <p class="cat-tarjeta__descripcion">${producto.descripcion}</p>
      <p class="cat-tarjeta__precio">
        <span class="cat-tarjeta__precio-moneda">S/</span>${precioFormateado}
      </p>
      <button
        class="cat-tarjeta__btn"
        id="btn-detalle-${producto.id}"
        aria-label="Ver detalles de ${producto.nombre}"
      >
        🔍 Ver detalles
      </button>
    </div>
  `;

  // Evento del botón "Ver detalles"
  const btnDetalle = article.querySelector('.cat-tarjeta__btn');
  btnDetalle.addEventListener('click', (e) => {
    e.stopPropagation(); // evita propagación innecesaria
    abrirModal(producto);
  });

  // También al hacer clic en la tarjeta completa
  article.addEventListener('click', () => abrirModal(producto));

  return article;
}

/* ═══════════════════════════════════════════════════
   MODAL DE DETALLES
   ═══════════════════════════════════════════════════ */

/**
 * Abre el modal y carga la información del producto.
 * @param {Object} producto - Datos del producto a mostrar
 */
function abrirModal(producto) {
  // Imagen principal (con fallback)
  elems.modalImagen.src     = producto.imagen;
  elems.modalImagen.alt     = producto.nombre;
  elems.modalImagen.onerror = () => { elems.modalImagen.src = IMAGEN_PLACEHOLDER; };

  // Textos
  elems.modalCategoria.textContent  = producto.categoria;
  elems.modalNombre.textContent     = producto.nombre;
  elems.modalPrecio.innerHTML       = `<span class="cat-modal__precio-moneda">S/</span>${formatearPrecio(producto.precio)}`;
  elems.modalDescripcion.textContent = producto.descripcion;

  // Lista de características
  elems.modalCaracteristicas.innerHTML = '';
  if (Array.isArray(producto.caracteristicas) && producto.caracteristicas.length > 0) {
    producto.caracteristicas.forEach((caract) => {
      const li = document.createElement('li');
      li.textContent = caract;
      elems.modalCaracteristicas.appendChild(li);
    });
  }

  // Mostramos el modal
  elems.modal.classList.add('activo');

  // Evitar scroll del fondo
  document.body.style.overflow = 'hidden';

  // Enfocamos el botón de cerrar (accesibilidad)
  elems.btnCerrarModal.focus();
}

/**
 * Cierra el modal de detalles.
 */
function cerrarModal() {
  elems.modal.classList.remove('activo');
  document.body.style.overflow = '';
}

/* ═══════════════════════════════════════════════════
   ESTADOS VISUALES (cargando, sin resultados, error)
   ═══════════════════════════════════════════════════ */

/** Muestra u oculta el spinner de carga */
function mostrarCargando(visible) {
  elems.cargando.style.display = visible ? 'flex' : 'none';
  elems.grid.style.display     = visible ? 'none' : 'grid';
}

/** Muestra u oculta el mensaje de "sin resultados" */
function mostrarSinResultados(visible) {
  if (visible) {
    elems.sinResultados.classList.add('visible');
  } else {
    elems.sinResultados.classList.remove('visible');
  }
}

/** Muestra un mensaje de error si no se puede cargar el JSON */
function mostrarError(mensaje) {
  elems.cargando.style.display = 'none';
  elems.grid.innerHTML = `
    <div style="grid-column:1/-1; text-align:center; padding:3rem; color:#888;">
      <p style="font-size:2rem; margin-bottom:0.5rem;">⚠️</p>
      <p style="font-weight:600;">${mensaje}</p>
    </div>
  `;
}

/** Actualiza el texto del contador de resultados */
function actualizarContador(cantidad) {
  if (!elems.contadorInfo) return;
  const total = estado.productos.length;
  if (cantidad === total) {
    elems.contadorInfo.innerHTML = `Mostrando <strong>${cantidad}</strong> productos`;
  } else {
    elems.contadorInfo.innerHTML = `Mostrando <strong>${cantidad}</strong> de <strong>${total}</strong> productos`;
  }
}

/* ═══════════════════════════════════════════════════
   RESET / LIMPIAR FILTROS
   ═══════════════════════════════════════════════════ */

/** Reinicia todos los filtros y vuelve al catálogo completo */
function resetearFiltros() {
  // Resetear estado
  estado.categoriaActiva = 'Todos';
  estado.textoBusqueda   = '';
  estado.orden           = 'relevancia';

  // Resetear UI
  if (elems.inputBusqueda) elems.inputBusqueda.value = '';
  if (elems.selectOrden)   elems.selectOrden.value   = 'relevancia';

  // Resetear botones de filtro (activar "Todos")
  elems.filtrosBtns.forEach((btn) => {
    btn.classList.toggle('activo', btn.dataset.categoria === 'Todos');
  });

  // Volver a renderizar con todos los productos
  filtrarYOrdenar();
}

/* ═══════════════════════════════════════════════════
   UTILIDADES
   ═══════════════════════════════════════════════════ */

/**
 * Formatea un número como precio con dos decimales.
 * Ej: 1299.9 → "1,299.90"
 * @param {number} precio
 * @returns {string}
 */
function formatearPrecio(precio) {
  return Number(precio).toLocaleString('es-PE', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });
}

/* ═══════════════════════════════════════════════════
   EVENTOS
   ═══════════════════════════════════════════════════ */

/**
 * Registra todos los event listeners del catálogo.
 * Se llama una sola vez al iniciar.
 */
function registrarEventos() {
  // ── Buscador (en tiempo real, con pequeño debounce) ──
  let temporizadorBusqueda;
  elems.inputBusqueda?.addEventListener('input', (e) => {
    clearTimeout(temporizadorBusqueda);
    temporizadorBusqueda = setTimeout(() => {
      estado.textoBusqueda = e.target.value;
      filtrarYOrdenar();
    }, 200); // 200ms de debounce para no sobre-filtrar mientras se escribe
  });

  // ── Filtros de categoría ──
  elems.filtrosBtns.forEach((btn) => {
    btn.addEventListener('click', () => {
      // Quitar activo de todos y activar el pulsado
      elems.filtrosBtns.forEach((b) => b.classList.remove('activo'));
      btn.classList.add('activo');

      estado.categoriaActiva = btn.dataset.categoria;
      filtrarYOrdenar();
    });
  });

  // ── Selector de ordenamiento ──
  elems.selectOrden?.addEventListener('change', (e) => {
    estado.orden = e.target.value;
    filtrarYOrdenar();
  });

  // ── Modal: cerrar con el botón X ──
  elems.btnCerrarModal?.addEventListener('click', cerrarModal);

  // ── Modal: cerrar al hacer clic en el overlay ──
  const overlay = document.getElementById('cat-modal-overlay');
  overlay?.addEventListener('click', cerrarModal);

  // ── Modal: cerrar con la tecla Escape ──
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && elems.modal.classList.contains('activo')) {
      cerrarModal();
    }
  });

  // ── Botón "Limpiar filtros" del estado sin resultados ──
  elems.btnReset?.addEventListener('click', resetearFiltros);
}

/* ═══════════════════════════════════════════════════
   INICIALIZACIÓN
   ═══════════════════════════════════════════════════ */

/**
 * Punto de entrada principal.
 * Se ejecuta cuando el DOM está completamente listo.
 */
document.addEventListener('DOMContentLoaded', () => {
  registrarEventos();
  cargarProductos();
});
