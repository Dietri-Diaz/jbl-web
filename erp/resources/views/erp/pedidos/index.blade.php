@extends('erp.layout')

@section('titulo', 'Pedidos')
@section('descripcion', 'Las compras que llegan desde la tienda. Confírmalas, despáchalas y márcalas como entregadas.')

@section('miga')
  <svg class="ico"><use href="#i-chevron"/></svg><span aria-current="page">Pedidos</span>
@endsection

@section('acciones')
  <a class="boton" href="{{ route('pedidos.exportar', request()->query()) }}">
    <svg class="ico"><use href="#i-descargar"/></svg> Exportar CSV
  </a>
@endsection

@section('contenido')

  {{-- ============ INDICADORES (según la búsqueda y las fechas) ============ --}}
  <section class="kpis" aria-label="Indicadores de pedidos">
    <div class="kpi">
      <span class="kpi__icono" aria-hidden="true"><svg class="ico"><use href="#i-pedidos"/></svg></span>
      <span class="kpi__etiqueta">Pedidos</span>
      <span class="kpi__valor">{{ $totalPedidos }}</span>
      <span class="kpi__nota">{{ $hayFiltros ? 'con los filtros aplicados' : 'desde que abrió la tienda' }}</span>
    </div>
    <div class="kpi kpi--oscuro">
      <span class="kpi__icono" aria-hidden="true"><svg class="ico"><use href="#i-dinero"/></svg></span>
      <span class="kpi__etiqueta">Vendido</span>
      <span class="kpi__valor"><small>S/</small>{{ number_format($vendido, 2) }}</span>
      <span class="kpi__nota">sin pedidos cancelados</span>
    </div>
    <div class="kpi">
      <span class="kpi__icono" aria-hidden="true"><svg class="ico"><use href="#i-estrella"/></svg></span>
      <span class="kpi__etiqueta">Ticket promedio</span>
      <span class="kpi__valor"><small>S/</small>{{ number_format($ticketPromedio, 2) }}</span>
      <span class="kpi__nota">por pedido vendido</span>
    </div>
    <a @class(['kpi', 'kpi--alerta' => $pendientes > 0]) href="{{ route('pedidos.index', array_merge($filtros, ['estado' => 'pendiente'])) }}">
      <span class="kpi__icono" aria-hidden="true"><svg class="ico"><use href="#i-alerta"/></svg></span>
      <span class="kpi__etiqueta">Pendientes</span>
      <span class="kpi__valor">{{ $pendientes }}</span>
      <span class="kpi__nota">{{ $pendientes > 0 ? 'Esperan confirmación' : 'Todo atendido' }}</span>
    </a>
  </section>

  {{-- ============ PESTAÑAS POR ESTADO ============ --}}
  <nav class="pestanas" aria-label="Filtrar por estado">
    <a href="{{ route('pedidos.index', $filtros) }}" @class(['pestana', 'activo' => $estado === 'todos']) @if ($estado === 'todos') aria-current="page" @endif>
      Todos <b>{{ $totalPedidos }}</b>
    </a>
    @foreach ($estados as $clave => $nombre)
      <a href="{{ route('pedidos.index', array_merge($filtros, ['estado' => $clave])) }}" @class(['pestana', 'activo' => $estado === $clave]) @if ($estado === $clave) aria-current="page" @endif>
        {{ $nombre }} <b>{{ $conteos->get($clave, 0) }}</b>
      </a>
    @endforeach
  </nav>

  <section class="tarjeta" aria-label="Listado de pedidos">

    {{-- ============ FILTROS ============ --}}
    <form class="filtros" method="GET" action="{{ route('pedidos.index') }}" role="search">
      {{-- La pestaña elegida se conserva al buscar. --}}
      @if ($estado !== 'todos')
        <input type="hidden" name="estado" value="{{ $estado }}">
      @endif

      <div class="filtros__buscar">
        <svg class="ico" aria-hidden="true"><use href="#i-buscar"/></svg>
        <label class="sr-only" for="q">Buscar pedidos</label>
        <input id="q" name="q" type="search" value="{{ $filtros['q'] ?? '' }}" placeholder="Buscar por código, cliente o correo">
      </div>

      <label for="desde" class="tenue">Desde</label>
      <input id="desde" name="desde" type="date" value="{{ $filtros['desde'] ?? '' }}">
      <label for="hasta" class="tenue">Hasta</label>
      <input id="hasta" name="hasta" type="date" value="{{ $filtros['hasta'] ?? '' }}">

      <button class="boton" type="submit">Filtrar</button>
      @if ($hayFiltros)
        <a class="filtros__limpiar" href="{{ route('pedidos.index', $estado !== 'todos' ? ['estado' => $estado] : []) }}">Limpiar</a>
      @endif
    </form>

    {{-- ============ TABLA ============ --}}
    @if ($pedidos->isEmpty())
      <div class="vacio">
        <span class="vacio__icono"><svg class="ico ico--grande"><use href="#i-pedidos"/></svg></span>
        @if ($hayFiltros)
          <p class="vacio__titulo">Ningún pedido coincide</p>
          <p>Prueba con otra búsqueda o cambia las fechas.</p>
          <a class="boton" href="{{ route('pedidos.index') }}">Quitar filtros</a>
        @elseif ($estado !== 'todos')
          <p class="vacio__titulo">No hay pedidos en estado {{ mb_strtolower($estados[$estado]) }}</p>
          <a class="boton" href="{{ route('pedidos.index') }}">Ver todos</a>
        @else
          <p class="vacio__titulo">Todavía no hay pedidos</p>
          <p>Aparecerán aquí cuando alguien compre en la tienda.</p>
        @endif
      </div>
    @else
      <div class="tabla-caja">
        <table class="tabla">
          <thead>
            <tr>
              <th scope="col">Pedido</th>
              <th scope="col">Cliente</th>
              <th scope="col">Entrega</th>
              <th scope="col">Pago</th>
              <th scope="col">Estado</th>
              <th scope="col" class="num">Total</th>
              <th scope="col"><span class="sr-only">Acciones</span></th>
            </tr>
          </thead>
          <tbody>
            @foreach ($pedidos as $pedido)
              <tr>
                <td>
                  <span class="tabla__principal"><a href="{{ route('pedidos.show', $pedido) }}">{{ $pedido->codigo }}</a></span>
                  <span class="tabla__secundario">
                    <time datetime="{{ $pedido->created_at->toIso8601String() }}" title="{{ $pedido->created_at->format('d/m/Y H:i') }}">{{ $pedido->created_at->diffForHumans() }}</time>
                  </span>
                </td>
                <td>
                  <span class="tabla__principal">{{ $pedido->cliente_nombre }}</span>
                  <span class="tabla__secundario">{{ $pedido->cliente_correo }}</span>
                </td>
                <td>
                  {{ $pedido->entrega === 'delivery' ? 'Envío a domicilio' : $pedido->nombreEntrega() }}
                  @if ($pedido->distrito)
                    <span class="tabla__secundario">{{ $pedido->distrito }}</span>
                  @endif
                </td>
                <td>{{ $pedido->nombrePago() }}</td>
                <td>@include('erp.partials.estado-pedido', ['estado' => $pedido->estado])</td>
                <td class="num negrita">S/ {{ number_format($pedido->total, 2) }}</td>
                <td>
                  <div class="tabla__acciones">
                    <a class="boton boton--icono boton--fantasma" href="{{ route('pedidos.show', $pedido) }}" title="Ver el pedido" aria-label="Ver el pedido {{ $pedido->codigo }}">
                      <svg class="ico"><use href="#i-ojo"/></svg>
                    </a>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      {{ $pedidos->links() }}
    @endif
  </section>
@endsection
