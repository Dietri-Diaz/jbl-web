@extends('erp.layout')

@section('titulo', 'Mensaje de '.$mensaje->nombre)
@section('descripcion', 'Recibido el '.$mensaje->created_at->format('d/m/Y').' a las '.$mensaje->created_at->format('H:i').' ('.$mensaje->created_at->diffForHumans().').')

@section('miga')
  <svg class="ico"><use href="#i-chevron"/></svg><a href="{{ route('mensajes.index') }}">Mensajes</a><svg class="ico"><use href="#i-chevron"/></svg><span aria-current="page">Detalle</span>
@endsection

@section('acciones')
  <a class="boton" href="{{ route('mensajes.index') }}">
    <svg class="ico"><use href="#i-volver"/></svg> Volver a mensajes
  </a>
  <a class="boton boton--primario" href="mailto:{{ rawurlencode($mensaje->correo) }}?subject={{ rawurlencode('Respuesta a tu mensaje a JBL') }}">
    <svg class="ico"><use href="#i-responder"/></svg> Responder por correo
  </a>
@endsection

@section('contenido')
  <div class="rejilla rejilla--2-1">

    {{-- ============ EL MENSAJE ============ --}}
    <article class="tarjeta" aria-labelledby="m-texto">
      <header class="tarjeta__cabecera">
        <h2 class="tarjeta__titulo" id="m-texto">Mensaje</h2>
        <span class="insignia">Leído</span>
      </header>
      {{-- nl2br respeta los saltos de línea que escribió la persona; e() evita que se ejecute HTML. --}}
      <div class="tarjeta__cuerpo">
        <p>{!! nl2br(e($mensaje->mensaje)) !!}</p>
      </div>
    </article>

    {{-- ============ QUIÉN ESCRIBE Y ACCIONES ============ --}}
    <div class="pila">
      <section class="tarjeta" aria-labelledby="m-remitente">
        <header class="tarjeta__cabecera">
          <h2 class="tarjeta__titulo" id="m-remitente">Remitente</h2>
        </header>
        <div class="tarjeta__cuerpo">
          <dl class="detalle detalle--1">
            <div>
              <dt>Nombre</dt>
              <dd>{{ $mensaje->nombre }}</dd>
            </div>
            <div>
              <dt>Correo</dt>
              <dd><a href="mailto:{{ rawurlencode($mensaje->correo) }}">{{ $mensaje->correo }}</a></dd>
            </div>
            <div>
              <dt>Recibido</dt>
              <dd><time datetime="{{ $mensaje->created_at->toIso8601String() }}">{{ $mensaje->created_at->format('d/m/Y H:i') }}</time></dd>
            </div>
          </dl>
        </div>
      </section>

      @if (auth()->user()->puedeEditar())
        <section class="tarjeta" aria-labelledby="m-acciones">
          <header class="tarjeta__cabecera">
            <h2 class="tarjeta__titulo" id="m-acciones">Acciones</h2>
          </header>
          <div class="tarjeta__cuerpo pila">
            {{-- "pila" estira cada botón a todo el ancho de la tarjeta. --}}
            <form class="pila" method="POST" action="{{ route('mensajes.leido', $mensaje) }}">
              @csrf @method('PATCH')
              <button class="boton" type="submit">
                <svg class="ico"><use href="#i-mensajes"/></svg> Marcar como no leído
              </button>
            </form>
            <form class="pila" method="POST" action="{{ route('mensajes.destroy', $mensaje) }}"
                  data-confirmar="¿Eliminar el mensaje de {{ $mensaje->nombre }}? No se puede deshacer."
                  data-confirmar-boton="Sí, eliminar">
              @csrf @method('DELETE')
              <button class="boton boton--peligro" type="submit">
                <svg class="ico"><use href="#i-borrar"/></svg> Eliminar mensaje
              </button>
            </form>
          </div>
        </section>
      @endif
    </div>
  </div>
@endsection
