@extends('erp.layout')

@section('titulo', 'Movimientos')
@section('descripcion', 'Historial del inventario: cada entrada, salida, venta y devolución, con quién la hizo y por qué.')

@section('miga')
  <svg class="ico"><use href="#i-chevron"/></svg><a href="{{ route('inventario.index') }}">Existencias</a><svg class="ico"><use href="#i-chevron"/></svg><span aria-current="page">Movimientos</span>
@endsection

@section('acciones')
  <a class="boton" href="{{ route('inventario.exportar', request()->query()) }}">
    <svg class="ico"><use href="#i-descargar"/></svg> Exportar CSV
  </a>
@endsection

@section('contenido')
  <section class="tarjeta" aria-label="Historial de movimientos">

    {{-- ============ FILTROS ============ --}}
    <form class="filtros" method="GET" action="{{ route('inventario.movimientos') }}">
      <label class="sr-only" for="tipo">Tipo de movimiento</label>
      <select id="tipo" name="tipo">
        <option value="">Todos los tipos</option>
        @foreach ($tipos as $valor => $texto)
          <option value="{{ $valor }}" @selected(($filtros['tipo'] ?? '') === $valor)>{{ $texto }}</option>
        @endforeach
      </select>

      <label class="sr-only" for="producto">Producto</label>
      <select id="producto" name="producto">
        <option value="">Todos los productos</option>
        @foreach ($productos as $producto)
          <option value="{{ $producto->id }}" @selected(($filtros['producto'] ?? '') == $producto->id)>{{ $producto->nombre }}</option>
        @endforeach
      </select>

      <label for="desde" class="tenue">Desde</label>
      <input id="desde" name="desde" type="date" value="{{ $filtros['desde'] ?? '' }}">
      <label for="hasta" class="tenue">Hasta</label>
      <input id="hasta" name="hasta" type="date" value="{{ $filtros['hasta'] ?? '' }}">

      <button class="boton" type="submit">Filtrar</button>
      @if ($hayFiltros)
        <a class="filtros__limpiar" href="{{ route('inventario.movimientos') }}">Limpiar</a>
      @endif
    </form>

    {{-- ============ TABLA ============ --}}
    @if ($movimientos->isEmpty())
      <div class="vacio">
        <span class="vacio__icono"><svg class="ico ico--grande"><use href="#i-movimientos"/></svg></span>
        @if ($hayFiltros)
          <p class="vacio__titulo">Ningún movimiento coincide</p>
          <p>Cambia el tipo, el producto o las fechas.</p>
          <a class="boton" href="{{ route('inventario.movimientos') }}">Quitar filtros</a>
        @else
          <p class="vacio__titulo">Todavía no hay movimientos</p>
          <p>Aparecerán aquí con la primera venta o el primer ajuste de stock.</p>
        @endif
      </div>
    @else
      <div class="tabla-caja">
        <table class="tabla">
          <thead>
            <tr>
              <th scope="col">Fecha</th>
              <th scope="col">Producto</th>
              <th scope="col">Tipo</th>
              <th scope="col" class="num">Cantidad</th>
              <th scope="col" class="num">Stock</th>
              <th scope="col">Motivo</th>
              <th scope="col">Hecho por</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($movimientos as $movimiento)
              <tr>
                <td class="sin-salto">
                  <time datetime="{{ $movimiento->created_at->toIso8601String() }}">
                    <span class="tabla__principal">{{ $movimiento->created_at->format('d/m/Y') }}</span>
                    <span class="tabla__secundario">{{ $movimiento->created_at->format('H:i') }}</span>
                  </time>
                </td>
                <td>
                  <span class="tabla__principal">
                    <a href="{{ route('inventario.ajustar', $movimiento->producto_id) }}">{{ $movimiento->producto?->nombre ?? 'Producto eliminado' }}</a>
                  </span>
                  @if ($movimiento->producto?->sku)
                    <span class="tabla__secundario">{{ $movimiento->producto->sku }}</span>
                  @endif
                </td>
                <td>@include('erp.partials.tipo-movimiento', ['movimiento' => $movimiento])</td>
                <td class="num">
                  <span @class(['mas' => $movimiento->cantidad > 0, 'menos' => $movimiento->cantidad < 0])>{{ $movimiento->cantidad > 0 ? '+' : '' }}{{ $movimiento->cantidad }}</span>
                </td>
                <td class="num">{{ $movimiento->stock_resultante }}</td>
                <td>{{ $movimiento->motivo ?: '—' }}</td>
                <td>
                  <span class="tabla__principal">{{ $movimiento->usuario?->name ?? 'Tienda' }}</span>
                  @if ($movimiento->pedido)
                    <span class="tabla__secundario"><a href="{{ route('pedidos.show', $movimiento->pedido) }}">{{ $movimiento->pedido->codigo }}</a></span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      {{ $movimientos->links() }}
    @endif
  </section>
@endsection
