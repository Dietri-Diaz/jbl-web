@extends('erp.layout')

@section('titulo', 'Existencias')
@section('descripcion', 'Cuánto hay de cada producto. El stock solo cambia con ventas, cancelaciones y ajustes: todo queda en el historial.')

@section('miga')
  <svg class="ico"><use href="#i-chevron"/></svg><span aria-current="page">Existencias</span>
@endsection

@section('acciones')
  <a class="boton" href="{{ route('inventario.movimientos') }}">
    <svg class="ico"><use href="#i-movimientos"/></svg> Ver movimientos
  </a>
@endsection

@section('contenido')
  @php($puedeEditar = auth()->user()->puedeEditar())

  {{-- ============ INDICADORES ============ --}}
  <section class="kpis" aria-label="Indicadores del inventario">
    <div class="kpi">
      <span class="kpi__icono" aria-hidden="true"><svg class="ico"><use href="#i-inventario"/></svg></span>
      <span class="kpi__etiqueta">Unidades en stock</span>
      <span class="kpi__valor">{{ number_format($unidades) }}</span>
      <span class="kpi__nota">sumando todos los productos</span>
    </div>
    <div class="kpi kpi--oscuro">
      <span class="kpi__icono" aria-hidden="true"><svg class="ico"><use href="#i-dinero"/></svg></span>
      <span class="kpi__etiqueta">Valor del inventario</span>
      <span class="kpi__valor"><small>S/</small>{{ number_format($valorInventario, 2) }}</span>
      <span class="kpi__nota">precio × stock</span>
    </div>
    <a @class(['kpi', 'kpi--alerta' => $stockBajo > 0]) href="{{ route('inventario.index', ['nivel' => 'bajo']) }}">
      <span class="kpi__icono" aria-hidden="true"><svg class="ico"><use href="#i-alerta"/></svg></span>
      <span class="kpi__etiqueta">Stock bajo</span>
      <span class="kpi__valor">{{ $stockBajo }}</span>
      <span class="kpi__nota">{{ $stockBajo > 0 ? 'En su mínimo o por debajo' : 'Nada por reponer' }}</span>
    </a>
    <a @class(['kpi', 'kpi--alerta' => $agotados > 0]) href="{{ route('inventario.index', ['nivel' => 'agotado']) }}">
      <span class="kpi__icono" aria-hidden="true"><svg class="ico"><use href="#i-menos"/></svg></span>
      <span class="kpi__etiqueta">Agotados</span>
      <span class="kpi__valor">{{ $agotados }}</span>
      <span class="kpi__nota">{{ $agotados > 0 ? 'Sin unidades para vender' : 'Todo tiene stock' }}</span>
    </a>
  </section>

  <section class="tarjeta" aria-label="Existencias por producto">

    {{-- ============ FILTROS ============ --}}
    <form class="filtros" method="GET" action="{{ route('inventario.index') }}" role="search">
      <div class="filtros__buscar">
        <svg class="ico" aria-hidden="true"><use href="#i-buscar"/></svg>
        <label class="sr-only" for="q">Buscar productos</label>
        <input id="q" name="q" type="search" value="{{ $filtros['q'] }}" placeholder="Buscar por nombre o SKU">
      </div>

      <label class="sr-only" for="categoria">Categoría</label>
      <select id="categoria" name="categoria">
        <option value="">Todas las categorías</option>
        @foreach ($categorias as $categoria)
          <option value="{{ $categoria->id }}" @selected($filtros['categoria'] == $categoria->id)>{{ $categoria->nombre }}</option>
        @endforeach
      </select>

      <label class="sr-only" for="nivel">Nivel de stock</label>
      <select id="nivel" name="nivel">
        <option value="todos">Todos los niveles</option>
        <option value="bajo" @selected($filtros['nivel'] === 'bajo')>Stock bajo (mínimo o menos)</option>
        <option value="agotado" @selected($filtros['nivel'] === 'agotado')>Agotados</option>
      </select>

      <button class="boton" type="submit">Filtrar</button>
      @if ($hayFiltros)
        <a class="filtros__limpiar" href="{{ route('inventario.index') }}">Limpiar</a>
      @endif
    </form>

    {{-- ============ TABLA ============ --}}
    @if ($productos->isEmpty())
      <div class="vacio">
        @if ($hayFiltros)
          <span class="vacio__icono"><svg class="ico ico--grande"><use href="#i-check"/></svg></span>
          <p class="vacio__titulo">Ningún producto coincide</p>
          <p>{{ $filtros['nivel'] === 'todos' ? 'Prueba con otra búsqueda o quita los filtros.' : 'No hay productos en ese nivel de stock con estos filtros.' }}</p>
          <a class="boton" href="{{ route('inventario.index') }}">Quitar filtros</a>
        @else
          <span class="vacio__icono"><svg class="ico ico--grande"><use href="#i-inventario"/></svg></span>
          <p class="vacio__titulo">Todavía no hay productos</p>
          <p>El inventario se llena al agregar productos al catálogo.</p>
        @endif
      </div>
    @else
      <div class="tabla-caja">
        <table class="tabla">
          <thead>
            <tr>
              <th scope="col">Producto</th>
              <th scope="col">Categoría</th>
              <th scope="col">Stock</th>
              <th scope="col" class="num">Mínimo</th>
              <th scope="col" class="num">Valor</th>
              <th scope="col">Último movimiento</th>
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
                      <span class="tabla__principal">{{ $producto->nombre }}</span>
                      <span class="tabla__secundario">{{ $producto->sku ?: 'Sin SKU' }}</span>
                    </div>
                  </div>
                </td>
                <td>{{ $producto->categoria?->nombre ?? 'Sin categoría' }}</td>
                <td>@include('erp.partials.nivel', ['producto' => $producto])</td>
                <td class="num">{{ $producto->stock_minimo }}</td>
                <td class="num">S/ {{ number_format($producto->precio * $producto->stock, 2) }}</td>
                <td>
                  @if ($producto->ultimoMovimiento)
                    <span class="tabla__principal">{{ $producto->ultimoMovimiento->nombreTipo() }}
                      <span @class(['mas' => $producto->ultimoMovimiento->cantidad > 0, 'menos' => $producto->ultimoMovimiento->cantidad < 0])>{{ $producto->ultimoMovimiento->cantidad > 0 ? '+' : '' }}{{ $producto->ultimoMovimiento->cantidad }}</span>
                    </span>
                    <span class="tabla__secundario">{{ $producto->ultimoMovimiento->created_at->diffForHumans() }}</span>
                  @else
                    <span class="tenue">Sin movimientos</span>
                  @endif
                </td>
                <td>
                  <div class="tabla__acciones">
                    <a class="boton boton--icono boton--fantasma" href="{{ route('inventario.movimientos', ['producto' => $producto->id]) }}" title="Ver historial" aria-label="Ver el historial de {{ $producto->nombre }}">
                      <svg class="ico"><use href="#i-bitacora"/></svg>
                    </a>
                    @if ($puedeEditar)
                      <a class="boton boton--chico" href="{{ route('inventario.ajustar', $producto) }}" aria-label="Ajustar el stock de {{ $producto->nombre }}">Ajustar</a>
                    @endif
                  </div>
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
