@extends('erp.layout')

@section('titulo', 'Productos')
@section('descripcion', 'El catálogo que se vende en la tienda: precios, fotos y qué está publicado.')

@section('miga')
  <svg class="ico"><use href="#i-chevron"/></svg><span aria-current="page">Productos</span>
@endsection

@section('acciones')
  <a class="boton" href="{{ route('productos.exportar', request()->query()) }}">
    <svg class="ico"><use href="#i-descargar"/></svg> Exportar CSV
  </a>
  @if (auth()->user()->puedeEditar())
    <a class="boton boton--primario" href="{{ route('productos.create') }}">
      <svg class="ico"><use href="#i-mas"/></svg> Nuevo producto
    </a>
  @endif
@endsection

@section('contenido')
  @php($puedeEditar = auth()->user()->puedeEditar())

  {{-- ============ INDICADORES ============ --}}
  <section class="kpis" aria-label="Indicadores del catálogo">
    <div class="kpi">
      <span class="kpi__icono" aria-hidden="true"><svg class="ico"><use href="#i-parlante"/></svg></span>
      <span class="kpi__etiqueta">Productos</span>
      <span class="kpi__valor">{{ $total }}</span>
      <span class="kpi__nota">en el catálogo</span>
    </div>
    <a class="kpi" href="{{ route('productos.index', ['estado' => 'publicados']) }}">
      <span class="kpi__icono" aria-hidden="true"><svg class="ico"><use href="#i-ojo"/></svg></span>
      <span class="kpi__etiqueta">Publicados</span>
      <span class="kpi__valor">{{ $publicados }}</span>
      <span class="kpi__nota">{{ $total - $publicados }} {{ $total - $publicados === 1 ? 'oculto' : 'ocultos' }}</span>
    </a>
    <div class="kpi">
      <span class="kpi__icono" aria-hidden="true"><svg class="ico"><use href="#i-estrella"/></svg></span>
      <span class="kpi__etiqueta">En oferta</span>
      <span class="kpi__valor">{{ $enOferta }}</span>
      <span class="kpi__nota">con precio rebajado</span>
    </div>
    <a @class(['kpi', 'kpi--alerta' => $agotados > 0]) href="{{ route('productos.index', ['stock' => 'agotado']) }}">
      <span class="kpi__icono" aria-hidden="true"><svg class="ico"><use href="#i-alerta"/></svg></span>
      <span class="kpi__etiqueta">Agotados</span>
      <span class="kpi__valor">{{ $agotados }}</span>
      <span class="kpi__nota">{{ $agotados > 0 ? 'Sin unidades para vender' : 'Todo tiene stock' }}</span>
    </a>
    <div class="kpi kpi--oscuro">
      <span class="kpi__icono" aria-hidden="true"><svg class="ico"><use href="#i-dinero"/></svg></span>
      <span class="kpi__etiqueta">Valor del inventario</span>
      <span class="kpi__valor"><small>S/</small>{{ number_format($valorInventario, 2) }}</span>
      <span class="kpi__nota">precio × stock</span>
    </div>
  </section>

  <section class="tarjeta" aria-label="Listado de productos">

    {{-- ============ FILTROS ============ --}}
    <form class="filtros" method="GET" action="{{ route('productos.index') }}" role="search">
      <div class="filtros__buscar">
        <svg class="ico" aria-hidden="true"><use href="#i-buscar"/></svg>
        <label class="sr-only" for="q">Buscar productos</label>
        <input id="q" name="q" type="search" value="{{ $filtros['q'] ?? '' }}" placeholder="Buscar por nombre, SKU o tipo">
      </div>

      <label class="sr-only" for="categoria">Categoría</label>
      <select id="categoria" name="categoria">
        <option value="">Todas las categorías</option>
        @foreach ($categorias as $categoria)
          <option value="{{ $categoria->id }}" @selected(($filtros['categoria'] ?? '') == $categoria->id)>{{ $categoria->nombre }}</option>
        @endforeach
      </select>

      <label class="sr-only" for="estado">Publicación</label>
      <select id="estado" name="estado">
        <option value="">Publicados y ocultos</option>
        <option value="publicados" @selected(($filtros['estado'] ?? '') === 'publicados')>Publicados</option>
        <option value="ocultos" @selected(($filtros['estado'] ?? '') === 'ocultos')>Ocultos</option>
      </select>

      <label class="sr-only" for="stock">Stock</label>
      <select id="stock" name="stock">
        <option value="">Cualquier stock</option>
        <option value="disponible" @selected(($filtros['stock'] ?? '') === 'disponible')>Disponible</option>
        <option value="bajo" @selected(($filtros['stock'] ?? '') === 'bajo')>Stock bajo</option>
        <option value="agotado" @selected(($filtros['stock'] ?? '') === 'agotado')>Agotado</option>
      </select>

      <label class="sr-only" for="orden">Ordenar por</label>
      <select id="orden" name="orden">
        @foreach ($ordenes as $valor => $texto)
          <option value="{{ $valor }}" @selected(($filtros['orden'] ?? 'orden') === $valor)>{{ $texto }}</option>
        @endforeach
      </select>

      <button class="boton" type="submit">Filtrar</button>
      @if ($hayFiltros)
        <a class="filtros__limpiar" href="{{ route('productos.index') }}">Limpiar</a>
      @endif
    </form>

    {{-- ============ TABLA ============ --}}
    @if ($productos->isEmpty())
      <div class="vacio">
        <span class="vacio__icono"><svg class="ico ico--grande"><use href="#i-parlante"/></svg></span>
        @if ($hayFiltros)
          <p class="vacio__titulo">Ningún producto coincide</p>
          <p>Prueba con otra búsqueda o quita los filtros.</p>
          <a class="boton" href="{{ route('productos.index') }}">Quitar filtros</a>
        @else
          <p class="vacio__titulo">Todavía no hay productos</p>
          <p>Agrega el primero para empezar a vender en la tienda.</p>
          @if ($puedeEditar)
            <a class="boton boton--primario" href="{{ route('productos.create') }}">
              <svg class="ico"><use href="#i-mas"/></svg> Nuevo producto
            </a>
          @endif
        @endif
      </div>
    @else
      <div class="tabla-caja">
        <table class="tabla">
          <thead>
            <tr>
              <th scope="col">Producto</th>
              <th scope="col">Categoría</th>
              <th scope="col" class="num">Precio</th>
              <th scope="col">Stock</th>
              <th scope="col">Estado</th>
              <th scope="col"><span class="sr-only">Acciones</span></th>
            </tr>
          </thead>
          <tbody>
            @foreach ($productos as $producto)
              <tr>
                <td>
                  <div class="tabla__producto">
                    @if ($producto->foto)
                      <img class="tabla__foto" src="{{ $producto->urlFoto() }}" alt="" width="52" height="52" loading="lazy">
                    @else
                      <span class="tabla__foto" aria-hidden="true"></span>
                    @endif
                    <div>
                      <span class="tabla__principal">
                        @if ($puedeEditar)
                          <a href="{{ route('productos.edit', $producto) }}">{{ $producto->nombre }}</a>
                        @else
                          {{ $producto->nombre }}
                        @endif
                      </span>
                      <span class="tabla__secundario">{{ $producto->sku ?: 'Sin SKU' }} · {{ $producto->tipo }}</span>
                    </div>
                  </div>
                </td>
                <td>{{ $producto->categoria?->nombre ?? 'Sin categoría' }}</td>
                <td class="num">
                  @if ($producto->enOferta())
                    <span class="precio-antes">S/ {{ number_format($producto->precio, 2) }}</span>
                    <span class="negrita">S/ {{ number_format($producto->precioFinal(), 2) }}</span>
                    <span class="insignia insignia--naranja insignia--sin-punto">−{{ $producto->descuento() }}%</span>
                  @else
                    <span class="negrita">S/ {{ number_format($producto->precio, 2) }}</span>
                  @endif
                </td>
                <td>@include('erp.partials.nivel', ['producto' => $producto])</td>
                <td>
                  <div class="fila">
                    <span @class(['insignia', 'insignia--ok' => $producto->visible])>{{ $producto->visible ? 'Publicado' : 'Oculto' }}</span>
                    @if ($producto->destacado)
                      <span class="insignia insignia--oscura insignia--sin-punto">Destacado</span>
                    @endif
                  </div>
                </td>
                <td>
                  @if ($puedeEditar)
                    <div class="tabla__acciones">
                      <form method="POST" action="{{ route('productos.visible', $producto) }}"
                            data-confirmar="{{ $producto->visible ? "¿Ocultar {$producto->nombre}? Dejará de verse en la tienda." : "¿Publicar {$producto->nombre}? Se verá en la tienda." }}"
                            data-confirmar-boton="{{ $producto->visible ? 'Sí, ocultar' : 'Sí, publicar' }}">
                        @csrf @method('PATCH')
                        <button class="boton boton--icono boton--fantasma" type="submit"
                                title="{{ $producto->visible ? 'Ocultar' : 'Publicar' }}"
                                aria-label="{{ $producto->visible ? 'Ocultar' : 'Publicar' }} {{ $producto->nombre }}">
                          <svg class="ico"><use href="#{{ $producto->visible ? 'i-ojo-no' : 'i-ojo' }}"/></svg>
                        </button>
                      </form>
                      <a class="boton boton--icono boton--fantasma" href="{{ route('productos.edit', $producto) }}" title="Editar" aria-label="Editar {{ $producto->nombre }}">
                        <svg class="ico"><use href="#i-editar"/></svg>
                      </a>
                      <form method="POST" action="{{ route('productos.destroy', $producto) }}"
                            data-confirmar="¿Eliminar {{ $producto->nombre }}? Se borran también su foto y su historial de inventario. No se puede deshacer."
                            data-confirmar-boton="Sí, eliminar">
                        @csrf @method('DELETE')
                        <button class="boton boton--icono boton--fantasma boton--peligro" type="submit" title="Eliminar" aria-label="Eliminar {{ $producto->nombre }}">
                          <svg class="ico"><use href="#i-borrar"/></svg>
                        </button>
                      </form>
                    </div>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      {{ $productos->links() }}
    @endif
  </section>
@endsection
