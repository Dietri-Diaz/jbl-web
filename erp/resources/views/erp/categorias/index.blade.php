@extends('erp.layout')

@section('titulo', 'Categorías')
@section('descripcion', 'Los grupos en los que la tienda ordena sus productos.')

@section('miga')
  <svg class="ico"><use href="#i-chevron"/></svg><span aria-current="page">Categorías</span>
@endsection

@section('acciones')
  @if (auth()->user()->puedeEditar())
    <a class="boton boton--primario" href="{{ route('categorias.create') }}">
      <svg class="ico"><use href="#i-mas"/></svg> Nueva categoría
    </a>
  @endif
@endsection

@section('contenido')
  @php($puedeEditar = auth()->user()->puedeEditar())

  {{-- ============ INDICADORES ============ --}}
  <section class="kpis" aria-label="Indicadores de categorías">
    <div class="kpi">
      <span class="kpi__icono" aria-hidden="true"><svg class="ico"><use href="#i-categorias"/></svg></span>
      <span class="kpi__etiqueta">Categorías</span>
      <span class="kpi__valor">{{ $total }}</span>
      <span class="kpi__nota">en total</span>
    </div>
    <div class="kpi">
      <span class="kpi__icono" aria-hidden="true"><svg class="ico"><use href="#i-ojo"/></svg></span>
      <span class="kpi__etiqueta">Visibles en la tienda</span>
      <span class="kpi__valor">{{ $visibles }}</span>
      <span class="kpi__nota">{{ $total - $visibles }} {{ $total - $visibles === 1 ? 'oculta' : 'ocultas' }}</span>
    </div>
    <div @class(['kpi', 'kpi--alerta' => $sinCategoria > 0])>
      <span class="kpi__icono" aria-hidden="true"><svg class="ico"><use href="#i-alerta"/></svg></span>
      <span class="kpi__etiqueta">Productos sin categoría</span>
      <span class="kpi__valor">{{ $sinCategoria }}</span>
      <span class="kpi__nota">{{ $sinCategoria > 0 ? 'No se muestran en la tienda' : 'Todos están clasificados' }}</span>
    </div>
  </section>

  <section class="tarjeta" aria-label="Listado de categorías">

    {{-- ============ FILTROS ============ --}}
    <form class="filtros" method="GET" action="{{ route('categorias.index') }}" role="search">
      <div class="filtros__buscar">
        <svg class="ico" aria-hidden="true"><use href="#i-buscar"/></svg>
        <label class="sr-only" for="q">Buscar categorías</label>
        <input id="q" name="q" type="search" value="{{ $filtros['q'] ?? '' }}" placeholder="Buscar por nombre o descripción">
      </div>

      <label class="sr-only" for="estado">Visibilidad</label>
      <select id="estado" name="estado">
        <option value="">Visibles y ocultas</option>
        <option value="visibles" @selected(($filtros['estado'] ?? '') === 'visibles')>Visibles</option>
        <option value="ocultas" @selected(($filtros['estado'] ?? '') === 'ocultas')>Ocultas</option>
      </select>

      <button class="boton" type="submit">Filtrar</button>
      @if ($hayFiltros)
        <a class="filtros__limpiar" href="{{ route('categorias.index') }}">Limpiar</a>
      @endif
    </form>

    {{-- ============ TABLA ============ --}}
    @if ($categorias->isEmpty())
      <div class="vacio">
        <span class="vacio__icono"><svg class="ico ico--grande"><use href="#i-categorias"/></svg></span>
        @if ($hayFiltros)
          <p class="vacio__titulo">Ninguna categoría coincide</p>
          <p>Prueba con otra búsqueda o quita los filtros.</p>
          <a class="boton" href="{{ route('categorias.index') }}">Quitar filtros</a>
        @else
          <p class="vacio__titulo">Todavía no hay categorías</p>
          <p>Crea la primera para poder agregar productos.</p>
          @if ($puedeEditar)
            <a class="boton boton--primario" href="{{ route('categorias.create') }}">
              <svg class="ico"><use href="#i-mas"/></svg> Nueva categoría
            </a>
          @endif
        @endif
      </div>
    @else
      <div class="tabla-caja">
        <table class="tabla">
          <thead>
            <tr>
              <th scope="col">Categoría</th>
              <th scope="col" class="num">Productos</th>
              <th scope="col">Estado</th>
              <th scope="col" class="num">Orden</th>
              <th scope="col"><span class="sr-only">Acciones</span></th>
            </tr>
          </thead>
          <tbody>
            @foreach ($categorias as $categoria)
              <tr>
                <td>
                  <span class="tabla__principal">
                    @if ($puedeEditar)
                      <a href="{{ route('categorias.edit', $categoria) }}">{{ $categoria->nombre }}</a>
                    @else
                      {{ $categoria->nombre }}
                    @endif
                  </span>
                  <span class="tabla__secundario">{{ $categoria->descripcion ?: 'Sin descripción' }}</span>
                </td>
                <td class="num">
                  @if ($categoria->productos_count > 0)
                    <a href="{{ route('productos.index', ['categoria' => $categoria->id]) }}">{{ $categoria->productos_count }}</a>
                  @else
                    <span class="tenue">0</span>
                  @endif
                </td>
                <td>
                  <span @class(['insignia', 'insignia--ok' => $categoria->visible])>{{ $categoria->visible ? 'Visible' : 'Oculta' }}</span>
                </td>
                <td class="num">{{ $categoria->orden }}</td>
                <td>
                  @if ($puedeEditar)
                    <div class="tabla__acciones">
                      <a class="boton boton--icono boton--fantasma" href="{{ route('categorias.edit', $categoria) }}" title="Editar" aria-label="Editar {{ $categoria->nombre }}">
                        <svg class="ico"><use href="#i-editar"/></svg>
                      </a>
                      <form method="POST" action="{{ route('categorias.destroy', $categoria) }}"
                            data-confirmar="¿Eliminar la categoría {{ $categoria->nombre }}? No se puede deshacer."
                            data-confirmar-boton="Sí, eliminar">
                        @csrf @method('DELETE')
                        <button class="boton boton--icono boton--fantasma boton--peligro" type="submit" title="Eliminar" aria-label="Eliminar {{ $categoria->nombre }}">
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

      {{ $categorias->links() }}
    @endif
  </section>
@endsection
