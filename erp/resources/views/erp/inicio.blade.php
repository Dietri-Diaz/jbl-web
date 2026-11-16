@extends('erp.layout')

@section('titulo', 'Inicio')
@section('descripcion', 'Resumen de la tienda: ventas, pedidos por atender y estado del inventario.')

@section('acciones')
  <a class="boton" href="{{ route('pedidos.index') }}">
    <svg class="ico"><use href="#i-pedidos"/></svg> Ver pedidos
  </a>
  @if (auth()->user()->puedeEditar())
    <a class="boton boton--primario" href="{{ route('productos.create') }}">
      <svg class="ico"><use href="#i-mas"/></svg> Nuevo producto
    </a>
  @endif
@endsection

@section('contenido')

  {{-- ============ INDICADORES ============ --}}
  <section class="kpis" aria-label="Indicadores">
    <div class="kpi kpi--oscuro">
      <span class="kpi__icono" aria-hidden="true"><svg class="ico"><use href="#i-dinero"/></svg></span>
      <span class="kpi__etiqueta">Ventas del mes</span>
      <span class="kpi__valor"><small>S/</small>{{ number_format($ventasMes, 2) }}</span>
      <span class="kpi__nota">{{ $pedidosMes }} {{ $pedidosMes === 1 ? 'pedido' : 'pedidos' }} · hoy S/ {{ number_format($ventasHoy, 2) }}</span>
    </div>

    <a class="kpi" href="{{ route('pedidos.index', ['estado' => 'pendiente']) }}">
      <span class="kpi__icono" aria-hidden="true"><svg class="ico"><use href="#i-pedidos"/></svg></span>
      <span class="kpi__etiqueta">Pedidos pendientes</span>
      <span class="kpi__valor">{{ $pendientes }}</span>
      <span class="kpi__nota">{{ $pendientes > 0 ? 'Esperan confirmación' : 'Todo atendido' }}</span>
    </a>

    <a class="kpi" href="{{ route('productos.index') }}">
      <span class="kpi__icono" aria-hidden="true"><svg class="ico"><use href="#i-parlante"/></svg></span>
      <span class="kpi__etiqueta">Productos publicados</span>
      <span class="kpi__valor">{{ $publicados }}</span>
      <span class="kpi__nota">de {{ $totalProductos }} en el catálogo</span>
    </a>

    <a @class(['kpi', 'kpi--alerta' => $stockBajo > 0]) href="{{ route('inventario.index', ['nivel' => 'bajo']) }}">
      <span class="kpi__icono" aria-hidden="true"><svg class="ico"><use href="#i-alerta"/></svg></span>
      <span class="kpi__etiqueta">Stock bajo</span>
      <span class="kpi__valor">{{ $stockBajo }}</span>
      <span class="kpi__nota">{{ $stockBajo > 0 ? 'Productos por reponer' : 'Inventario en orden' }}</span>
    </a>

    <a class="kpi" href="{{ route('mensajes.index') }}">
      <span class="kpi__icono" aria-hidden="true"><svg class="ico"><use href="#i-mensajes"/></svg></span>
      <span class="kpi__etiqueta">Mensajes sin leer</span>
      <span class="kpi__valor">{{ $noLeidos }}</span>
      <span class="kpi__nota">del formulario de contacto</span>
    </a>
  </section>

  {{-- ============ GRÁFICOS ============ --}}
  <div class="rejilla rejilla--2-1">
    <section class="tarjeta" aria-labelledby="g-ventas">
      <header class="tarjeta__cabecera">
        <h2 class="tarjeta__titulo" id="g-ventas">Ventas de los últimos 14 días</h2>
        <span class="tarjeta__nota">Sin pedidos cancelados</span>
      </header>
      <div class="tarjeta__cuerpo">
        <div class="grafico"><canvas data-grafico='@json($graficoVentas)' role="img" aria-label="Gráfico de línea con el total vendido por día"></canvas></div>
      </div>
    </section>

    <section class="tarjeta" aria-labelledby="g-estados">
      <header class="tarjeta__cabecera">
        <h2 class="tarjeta__titulo" id="g-estados">Pedidos por estado</h2>
      </header>
      <div class="tarjeta__cuerpo">
        @if (array_sum($graficoEstados['valores']) > 0)
          <div class="grafico"><canvas data-grafico='@json($graficoEstados)' role="img" aria-label="Gráfico circular con la cantidad de pedidos en cada estado"></canvas></div>
        @else
          <div class="vacio">
            <span class="vacio__icono"><svg class="ico ico--grande"><use href="#i-pedidos"/></svg></span>
            <p class="vacio__titulo">Aún no hay pedidos</p>
            <p>Aparecerán aquí cuando alguien compre en la tienda.</p>
          </div>
        @endif
      </div>
    </section>
  </div>

  {{-- ============ PEDIDOS Y STOCK ============ --}}
  <div class="rejilla rejilla--2-1">
    <section class="tarjeta" aria-labelledby="t-pedidos">
      <header class="tarjeta__cabecera">
        <h2 class="tarjeta__titulo" id="t-pedidos">Últimos pedidos</h2>
        <a href="{{ route('pedidos.index') }}">Ver todos</a>
      </header>
      @if ($ultimosPedidos->isEmpty())
        <div class="vacio">
          <p class="vacio__titulo">Sin pedidos todavía</p>
        </div>
      @else
        <div class="tabla-caja">
          <table class="tabla">
            <thead>
              <tr>
                <th scope="col">Pedido</th>
                <th scope="col">Cliente</th>
                <th scope="col">Estado</th>
                <th scope="col" class="num">Total</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($ultimosPedidos as $pedido)
                <tr>
                  <td>
                    <span class="tabla__principal"><a href="{{ route('pedidos.show', $pedido) }}">{{ $pedido->codigo }}</a></span>
                    <span class="tabla__secundario">{{ $pedido->created_at->diffForHumans() }}</span>
                  </td>
                  <td>{{ $pedido->cliente_nombre }}</td>
                  <td>@include('erp.partials.estado-pedido', ['estado' => $pedido->estado])</td>
                  <td class="num negrita">S/ {{ number_format($pedido->total, 2) }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </section>

    <section class="tarjeta" aria-labelledby="t-stock">
      <header class="tarjeta__cabecera">
        <h2 class="tarjeta__titulo" id="t-stock">Por reponer</h2>
        <a href="{{ route('inventario.index', ['nivel' => 'bajo']) }}">Ver inventario</a>
      </header>
      @if ($productosBajos->isEmpty())
        <div class="vacio">
          <span class="vacio__icono"><svg class="ico ico--grande"><use href="#i-check"/></svg></span>
          <p class="vacio__titulo">Inventario en orden</p>
          <p>Ningún producto está en su stock mínimo.</p>
        </div>
      @else
        <ul class="lista">
          @foreach ($productosBajos as $producto)
            <li>
              @if ($producto->foto)
                <img class="lista__foto" src="{{ $producto->urlFoto() }}" alt="" width="44" height="44" loading="lazy">
              @else
                <span class="lista__foto" aria-hidden="true"></span>
              @endif
              <span class="lista__texto">
                <span class="lista__principal"><a href="{{ route('inventario.ajustar', $producto) }}">{{ $producto->nombre }}</a></span>
                <span class="lista__secundario">Mínimo {{ $producto->stock_minimo }}</span>
              </span>
              <span @class(['insignia', 'insignia--error' => $producto->stock === 0, 'insignia--alerta' => $producto->stock > 0])>
                {{ $producto->stock === 0 ? 'Agotado' : $producto->stock.' uds.' }}
              </span>
            </li>
          @endforeach
        </ul>
      @endif
    </section>
  </div>

  {{-- ============ CATÁLOGO, MÁS VENDIDOS Y ACTIVIDAD ============ --}}
  <div class="rejilla rejilla--3">
    <section class="tarjeta" aria-labelledby="g-categorias">
      <header class="tarjeta__cabecera">
        <h2 class="tarjeta__titulo" id="g-categorias">Productos por categoría</h2>
      </header>
      <div class="tarjeta__cuerpo">
        <div class="grafico grafico--bajo"><canvas data-grafico='@json($graficoCategorias)' role="img" aria-label="Gráfico de barras con la cantidad de productos de cada categoría"></canvas></div>
      </div>
    </section>

    <section class="tarjeta" aria-labelledby="t-vendidos">
      <header class="tarjeta__cabecera">
        <h2 class="tarjeta__titulo" id="t-vendidos">Más vendidos</h2>
        <span class="tarjeta__nota">Unidades</span>
      </header>
      @if ($masVendidos->isEmpty())
        <div class="vacio"><p class="vacio__titulo">Sin ventas todavía</p></div>
      @else
        <ol class="lista">
          @foreach ($masVendidos as $i => $item)
            <li>
              <span class="avatar" aria-hidden="true">{{ $i + 1 }}</span>
              <span class="lista__texto">
                <span class="lista__principal">{{ $item->nombre }}</span>
                <span class="lista__secundario">S/ {{ number_format($item->importe, 2) }}</span>
              </span>
              <span class="lista__dato">{{ $item->unidades }}</span>
            </li>
          @endforeach
        </ol>
      @endif
    </section>

    <section class="tarjeta" aria-labelledby="t-actividad">
      <header class="tarjeta__cabecera">
        <h2 class="tarjeta__titulo" id="t-actividad">Actividad reciente</h2>
        @if (auth()->user()->esAdmin())
          <a href="{{ route('bitacora.index') }}">Bitácora</a>
        @endif
      </header>
      @if ($actividad->isEmpty())
        <div class="vacio"><p class="vacio__titulo">Sin actividad registrada</p></div>
      @else
        <ul class="historial">
          @foreach ($actividad as $registro)
            <li>
              <span>{{ $registro->descripcion }}</span>
              <time datetime="{{ $registro->created_at->toIso8601String() }}">{{ $registro->usuario }} · {{ $registro->created_at->diffForHumans() }}</time>
            </li>
          @endforeach
        </ul>
      @endif
    </section>
  </div>
@endsection

@push('scripts')
  <script src="{{ asset('js/vendor/chart.umd.min.js') }}"></script>
@endpush
