@extends('erp.layout')

@section('titulo', 'Pedido '.$pedido->codigo)
@section('descripcion', 'Recibido el '.$pedido->created_at->format('d/m/Y').' a las '.$pedido->created_at->format('H:i').' ('.$pedido->created_at->diffForHumans().').')

@section('miga')
  <svg class="ico"><use href="#i-chevron"/></svg><a href="{{ route('pedidos.index') }}">Pedidos</a><svg class="ico"><use href="#i-chevron"/></svg><span aria-current="page">{{ $pedido->codigo }}</span>
@endsection

@section('acciones')
  <span class="fila">@include('erp.partials.estado-pedido', ['estado' => $pedido->estado])</span>
  <a class="boton" href="{{ route('pedidos.index') }}">
    <svg class="ico"><use href="#i-volver"/></svg> Volver a pedidos
  </a>
@endsection

@section('contenido')
  @php
    // Camino normal de un pedido; "cancelado" queda fuera porque lo interrumpe.
    $camino = ['pendiente', 'confirmado', 'enviado', 'entregado'];
    $posicion = array_search($pedido->estado, $camino, true);

    // Texto y aspecto del botón de cada cambio de estado.
    $botones = [
        'confirmado' => ['texto' => 'Confirmar pedido', 'clase' => 'boton--primario', 'icono' => 'i-check',
            'pregunta' => "¿Confirmar el pedido {$pedido->codigo}? Significa que ya verificaste el pago o los datos."],
        'enviado' => ['texto' => 'Marcar como enviado', 'clase' => 'boton--primario', 'icono' => 'i-camion',
            'pregunta' => "¿Marcar el pedido {$pedido->codigo} como enviado?"],
        'entregado' => ['texto' => 'Marcar como entregado', 'clase' => 'boton--primario', 'icono' => 'i-check',
            'pregunta' => "¿Marcar el pedido {$pedido->codigo} como entregado? Después ya no se puede cambiar."],
        'cancelado' => ['texto' => 'Cancelar pedido', 'clase' => 'boton--peligro', 'icono' => 'i-borrar',
            'pregunta' => "¿Cancelar el pedido {$pedido->codigo}? Sus productos vuelven al inventario y no se puede deshacer."],
    ];

    $soloDigitos = preg_replace('/\D/', '', $pedido->cliente_telefono);
  @endphp

  {{-- ============ AVANCE DEL PEDIDO ============ --}}
  @if ($pedido->estado === 'cancelado')
    <div class="nota nota--error" role="status">
      <svg class="ico"><use href="#i-alerta"/></svg>
      Este pedido fue cancelado. Sus productos volvieron al inventario.
    </div>
  @else
    <div class="rejilla">
    <section class="tarjeta" aria-label="Avance del pedido">
      <ol class="pasos">
        @foreach ($camino as $i => $paso)
          {{-- Lo ya superado va "hecho"; el paso en curso, "actual". Entregado es el final: también queda hecho. --}}
          <li @class(['hecho' => $i < $posicion || $pedido->estado === 'entregado', 'actual' => $i === $posicion && $pedido->estado !== 'entregado'])
              @if ($i === $posicion) aria-current="step" @endif>
            {{ config("tienda.estados.$paso") }}
          </li>
        @endforeach
      </ol>
    </section>
    </div>
  @endif

  <div class="rejilla rejilla--2-1">

    {{-- ============ COLUMNA PRINCIPAL ============ --}}
    <div class="pila">
      <section class="tarjeta" aria-labelledby="p-productos">
        <header class="tarjeta__cabecera">
          <h2 class="tarjeta__titulo" id="p-productos">Productos</h2>
          <span class="tarjeta__nota">{{ $pedido->items->sum('cantidad') }} {{ $pedido->items->sum('cantidad') === 1 ? 'unidad' : 'unidades' }}</span>
        </header>
        <div class="tabla-caja">
          <table class="tabla">
            <thead>
              <tr>
                <th scope="col">Producto</th>
                <th scope="col" class="num">Precio</th>
                <th scope="col" class="num">Cantidad</th>
                <th scope="col" class="num">Subtotal</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($pedido->items as $item)
                <tr>
                  <td>
                    <div class="tabla__producto">
                      @if ($item->producto?->foto)
                        <img class="tabla__foto" src="{{ $item->producto->urlFoto() }}" alt="" width="52" height="52" loading="lazy">
                      @else
                        <span class="tabla__foto" aria-hidden="true"></span>
                      @endif
                      <div>
                        <span class="tabla__principal">{{ $item->nombre }}</span>
                        <span class="tabla__secundario">{{ $item->producto ? ($item->producto->sku ?: 'Sin SKU') : 'Ya no está en el catálogo' }}</span>
                      </div>
                    </div>
                  </td>
                  <td class="num">S/ {{ number_format($item->precio_unitario, 2) }}</td>
                  <td class="num">{{ $item->cantidad }}</td>
                  <td class="num negrita">S/ {{ number_format($item->subtotal, 2) }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <dl class="totales">
          <div>
            <dt>Subtotal</dt>
            <dd>S/ {{ number_format($pedido->subtotal, 2) }}</dd>
          </div>
          <div>
            <dt>Envío</dt>
            <dd>{{ $pedido->envio > 0 ? 'S/ '.number_format($pedido->envio, 2) : 'Gratis' }}</dd>
          </div>
          <div class="totales__total">
            <dt>Total</dt>
            <dd>S/ {{ number_format($pedido->total, 2) }}</dd>
          </div>
        </dl>
      </section>

      {{-- ============ HISTORIAL ============ --}}
      <section class="tarjeta" aria-labelledby="p-historial">
        <header class="tarjeta__cabecera">
          <h2 class="tarjeta__titulo" id="p-historial">Historial del pedido</h2>
          <span class="tarjeta__nota">Del más antiguo al más reciente</span>
        </header>
        <ol class="historial">
          @foreach ($historial as $evento)
            <li>
              <span>{{ $evento['texto'] }}</span>
              <time datetime="{{ $evento['fecha']->toIso8601String() }}">{{ $evento['autor'] }} · {{ $evento['fecha']->format('d/m/Y H:i') }}</time>
            </li>
          @endforeach
        </ol>
      </section>
    </div>

    {{-- ============ COLUMNA LATERAL ============ --}}
    <div class="pila">

      <section class="tarjeta" aria-labelledby="p-estado">
        <header class="tarjeta__cabecera">
          <h2 class="tarjeta__titulo" id="p-estado">Cambiar estado</h2>
        </header>
        <div class="tarjeta__cuerpo pila">
          @if (empty($pedido->siguientesEstados()))
            <p class="tenue">Este pedido está {{ mb_strtolower($pedido->nombreEstado()) }}: ya no cambia de estado.</p>
          @elseif (! auth()->user()->puedeEditar())
            <p class="tenue">Tu rol es de solo lectura: no puedes cambiar el estado.</p>
          @else
            @foreach ($pedido->siguientesEstados() as $siguiente)
              {{-- "pila" estira el botón a todo el ancho de la tarjeta. --}}
              <form class="pila" method="POST" action="{{ route('pedidos.estado', $pedido) }}"
                    data-confirmar="{{ $botones[$siguiente]['pregunta'] }}"
                    data-confirmar-boton="Sí, {{ mb_strtolower($botones[$siguiente]['texto']) }}">
                @csrf @method('PATCH')
                <input type="hidden" name="estado" value="{{ $siguiente }}">
                <button class="boton {{ $botones[$siguiente]['clase'] }}" type="submit">
                  <svg class="ico"><use href="#{{ $botones[$siguiente]['icono'] }}"/></svg> {{ $botones[$siguiente]['texto'] }}
                </button>
              </form>
            @endforeach
          @endif
        </div>
      </section>

      <section class="tarjeta" aria-labelledby="p-cliente">
        <header class="tarjeta__cabecera">
          <h2 class="tarjeta__titulo" id="p-cliente">Cliente</h2>
        </header>
        <div class="tarjeta__cuerpo pila">
          <dl class="detalle detalle--1">
            <div>
              <dt>Nombre</dt>
              <dd>{{ $pedido->cliente_nombre }}</dd>
            </div>
            <div>
              <dt>Correo</dt>
              <dd><a href="mailto:{{ rawurlencode($pedido->cliente_correo) }}">{{ $pedido->cliente_correo }}</a></dd>
            </div>
            <div>
              <dt>Celular</dt>
              <dd class="tabular">{{ $pedido->cliente_telefono }}</dd>
            </div>
            @if ($pedido->cliente_documento)
              <div>
                <dt>{{ strlen($pedido->cliente_documento) === 11 ? 'RUC' : 'DNI' }}</dt>
                <dd class="tabular">{{ $pedido->cliente_documento }}</dd>
              </div>
            @endif
          </dl>
          <div class="fila">
            <a class="boton boton--chico" href="mailto:{{ rawurlencode($pedido->cliente_correo) }}?subject={{ rawurlencode('Tu pedido '.$pedido->codigo.' en JBL') }}">
              <svg class="ico"><use href="#i-mensajes"/></svg> Correo
            </a>
            @if ($soloDigitos !== '')
              <a class="boton boton--chico" href="https://wa.me/51{{ $soloDigitos }}?text={{ rawurlencode('Hola '.$pedido->cliente_nombre.', te escribimos de JBL por tu pedido '.$pedido->codigo.'.') }}" target="_blank" rel="noopener">
                <svg class="ico"><use href="#i-telefono"/></svg> WhatsApp
              </a>
            @endif
          </div>
        </div>
      </section>

      <section class="tarjeta" aria-labelledby="p-entrega">
        <header class="tarjeta__cabecera">
          <h2 class="tarjeta__titulo" id="p-entrega">Entrega</h2>
        </header>
        <div class="tarjeta__cuerpo">
          <dl class="detalle detalle--1">
            <div>
              <dt>Modalidad</dt>
              <dd>{{ $pedido->nombreEntrega() }}</dd>
            </div>
            @if ($pedido->entrega === 'delivery')
              <div>
                <dt>Dirección</dt>
                <dd>{{ $pedido->direccion }}{{ $pedido->distrito ? ', '.$pedido->distrito : '' }}</dd>
              </div>
            @endif
            @if ($pedido->referencia)
              <div>
                <dt>Referencia</dt>
                <dd>{{ $pedido->referencia }}</dd>
              </div>
            @endif
            @if ($pedido->notas)
              <div>
                <dt>Notas del cliente</dt>
                <dd>{{ $pedido->notas }}</dd>
              </div>
            @endif
          </dl>
        </div>
      </section>

      <section class="tarjeta" aria-labelledby="p-pago">
        <header class="tarjeta__cabecera">
          <h2 class="tarjeta__titulo" id="p-pago">Pago</h2>
        </header>
        <div class="tarjeta__cuerpo">
          <dl class="detalle detalle--1">
            <div>
              <dt>Medio de pago</dt>
              <dd>{{ $pedido->nombrePago() }}</dd>
            </div>
            <div>
              <dt>Total a cobrar</dt>
              <dd class="negrita tabular">S/ {{ number_format($pedido->total, 2) }}</dd>
            </div>
          </dl>
        </div>
      </section>
    </div>
  </div>
@endsection
