@extends('erp.layout')

@section('titulo', 'Mensaje de '.$mensaje->nombre)
@section('miga')
  <a href="{{ route('mensajes.index') }}">Mensajes</a> / Detalle
@endsection

@section('contenido')
  <article class="mensaje-detalle">
    <dl class="mensaje-detalle__datos">
      <dt>De</dt>
      <dd>{{ $mensaje->nombre }}</dd>
      <dt>Correo</dt>
      <dd><a href="mailto:{{ $mensaje->correo }}">{{ $mensaje->correo }}</a></dd>
      <dt>Recibido</dt>
      <dd>{{ $mensaje->created_at->format('d/m/Y \a \l\a\s H:i') }} ({{ $mensaje->created_at->diffForHumans() }})</dd>
    </dl>

    <p class="mensaje-detalle__cuerpo">{{ $mensaje->mensaje }}</p>

    <div class="formulario__acciones">
      <a class="boton boton--primario" href="mailto:{{ $mensaje->correo }}?subject={{ rawurlencode('Respuesta a tu mensaje a JBL') }}">
        <svg class="ico"><use href="#i-mensajes"/></svg> Responder por correo
      </a>
      <form class="en-linea" method="POST" action="{{ route('mensajes.leido', $mensaje) }}">
        @csrf @method('PATCH')
        <button class="boton" type="submit">Marcar como no leído</button>
      </form>
      <form class="en-linea" method="POST" action="{{ route('mensajes.destroy', $mensaje) }}"
            onsubmit="return confirm('¿Eliminar este mensaje? No se puede deshacer.')">
        @csrf @method('DELETE')
        <button class="boton boton--peligro" type="submit">
          <svg class="ico"><use href="#i-borrar"/></svg> Eliminar
        </button>
      </form>
      <a class="boton" href="{{ route('mensajes.index') }}">
        <svg class="ico"><use href="#i-volver"/></svg> Volver
      </a>
    </div>
  </article>
@endsection
