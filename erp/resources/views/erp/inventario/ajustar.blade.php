@extends('erp.layout')

@section('titulo', 'Ajustar stock')
@section('descripcion', 'Registra mercadería que entra, que sale, o corrige el stock después de un conteo.')

@section('miga')
  <svg class="ico"><use href="#i-chevron"/></svg><a href="{{ route('inventario.index') }}">Existencias</a><svg class="ico"><use href="#i-chevron"/></svg><span aria-current="page">Ajustar</span>
@endsection

@section('contenido')
  @php($puedeEditar = auth()->user()->puedeEditar())

  <form class="formulario" method="POST" action="{{ route('inventario.guardar', $producto) }}" novalidate>
    @csrf

    {{-- ============ COLUMNA PRINCIPAL ============ --}}
    <div class="formulario__columna">
      <section class="tarjeta" aria-labelledby="f-movimiento">
        <header class="tarjeta__cabecera">
          <h2 class="tarjeta__titulo" id="f-movimiento">Movimiento</h2>
          <span class="tarjeta__nota">Stock actual: <b class="tabular">{{ $producto->stock }}</b></span>
        </header>
        <div class="tarjeta__cuerpo pila">

          <div class="campo" role="radiogroup" aria-labelledby="tipo-etiqueta" @error('tipo') aria-describedby="tipo-error" @enderror>
            <span class="campo__etiqueta" id="tipo-etiqueta">Tipo de movimiento</span>
            <div class="opciones opciones--fila">
              <label class="opcion">
                <input type="radio" name="tipo" value="entrada" @checked(old('tipo', 'entrada') === 'entrada')>
                <span class="opcion__caja">
                  <b>Entrada</b>
                  <span>Llegó mercadería: suma al stock.</span>
                </span>
              </label>
              <label class="opcion">
                <input type="radio" name="tipo" value="salida" @checked(old('tipo') === 'salida')>
                <span class="opcion__caja">
                  <b>Salida</b>
                  <span>Merma, daño o muestra: resta del stock.</span>
                </span>
              </label>
              <label class="opcion">
                <input type="radio" name="tipo" value="ajuste" @checked(old('tipo') === 'ajuste')>
                <span class="opcion__caja">
                  <b>Ajuste</b>
                  <span>Tras un conteo: fija el stock en un número exacto.</span>
                </span>
              </label>
            </div>
            @error('tipo') <p class="campo__error" id="tipo-error">{{ $message }}</p> @enderror
          </div>

          <div class="campos">
            <div class="campo">
              <label for="cantidad">Cantidad</label>
              <input id="cantidad" name="cantidad" type="number" min="0" max="99999" inputmode="numeric" required
                     value="{{ old('cantidad') }}"
                     aria-describedby="cantidad-ayuda @error('cantidad') cantidad-error @enderror"
                     @error('cantidad') aria-invalid="true" @enderror>
              <p class="campo__ayuda" id="cantidad-ayuda">En entrada y salida, las unidades que se mueven. En ajuste, el stock que debe quedar.</p>
              @error('cantidad') <p class="campo__error" id="cantidad-error">{{ $message }}</p> @enderror
            </div>

            <div class="campo">
              <label for="motivo">Motivo</label>
              <input id="motivo" name="motivo" type="text" maxlength="160" required
                     value="{{ old('motivo') }}" placeholder="Compra al proveedor, factura F001-245"
                     aria-describedby="motivo-ayuda @error('motivo') motivo-error @enderror"
                     @error('motivo') aria-invalid="true" @enderror>
              <p class="campo__ayuda" id="motivo-ayuda">Queda en el historial junto a tu nombre.</p>
              @error('motivo') <p class="campo__error" id="motivo-error">{{ $message }}</p> @enderror
            </div>
          </div>
        </div>
      </section>
    </div>

    {{-- ============ COLUMNA LATERAL: FICHA DEL PRODUCTO ============ --}}
    <div class="formulario__columna">
      <section class="tarjeta" aria-labelledby="f-producto">
        <header class="tarjeta__cabecera">
          <h2 class="tarjeta__titulo" id="f-producto">Producto</h2>
          <span @class(['insignia', 'insignia--ok' => $producto->visible])>{{ $producto->visible ? 'Publicado' : 'Oculto' }}</span>
        </header>
        <ul class="lista">
          <li>
            @if ($producto->foto)
              <img class="lista__foto" src="{{ $producto->urlFoto() }}" alt="" width="44" height="44">
            @else
              <span class="lista__foto" aria-hidden="true"></span>
            @endif
            <span class="lista__texto">
              <span class="lista__principal">{{ $producto->nombre }}</span>
              <span class="lista__secundario">{{ $producto->sku ?: 'Sin SKU' }} · {{ $producto->categoria?->nombre ?? 'Sin categoría' }}</span>
            </span>
          </li>
        </ul>
        <div class="tarjeta__cuerpo">
          <dl class="detalle">
            <div>
              <dt>Stock actual</dt>
              <dd>@include('erp.partials.nivel', ['producto' => $producto])</dd>
            </div>
            <div>
              <dt>Stock mínimo</dt>
              <dd class="tabular">{{ $producto->stock_minimo }}</dd>
            </div>
            <div>
              <dt>Precio</dt>
              <dd class="tabular">S/ {{ number_format($producto->precioFinal(), 2) }}</dd>
            </div>
            <div>
              <dt>Valor en stock</dt>
              <dd class="tabular">S/ {{ number_format($producto->precio * $producto->stock, 2) }}</dd>
            </div>
          </dl>
        </div>
      </section>

      <section class="tarjeta" aria-labelledby="f-ultimos">
        <header class="tarjeta__cabecera">
          <h2 class="tarjeta__titulo" id="f-ultimos">Últimos movimientos</h2>
          <a href="{{ route('inventario.movimientos', ['producto' => $producto->id]) }}">Ver todos</a>
        </header>
        @if ($ultimos->isEmpty())
          <div class="vacio">
            <p class="vacio__titulo">Sin movimientos</p>
            <p>Este producto todavía no tuvo entradas ni salidas.</p>
          </div>
        @else
          <ul class="lista">
            @foreach ($ultimos as $movimiento)
              <li>
                <span class="lista__texto">
                  <span class="lista__principal">{{ $movimiento->nombreTipo() }}{{ $movimiento->motivo ? ' · '.$movimiento->motivo : '' }}</span>
                  <span class="lista__secundario">{{ $movimiento->usuario?->name ?? 'Tienda' }} · {{ $movimiento->created_at->diffForHumans() }} · quedó en {{ $movimiento->stock_resultante }}</span>
                </span>
                <span @class(['lista__dato', 'mas' => $movimiento->cantidad > 0, 'menos' => $movimiento->cantidad < 0])>{{ $movimiento->cantidad > 0 ? '+' : '' }}{{ $movimiento->cantidad }}</span>
              </li>
            @endforeach
          </ul>
        @endif
      </section>
    </div>

    <div class="formulario__pie">
      <a class="boton" href="{{ route('inventario.index') }}">{{ $puedeEditar ? 'Cancelar' : 'Volver' }}</a>
      @if ($puedeEditar)
        <button class="boton boton--primario" type="submit">
          <svg class="ico"><use href="#i-check"/></svg> Registrar movimiento
        </button>
      @endif
    </div>
  </form>
@endsection
