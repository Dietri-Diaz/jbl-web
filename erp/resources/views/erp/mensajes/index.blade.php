@extends('erp.layout')

@section('titulo', 'Mensajes')
@section('descripcion', 'Lo que escriben los visitantes en el formulario de contacto de la tienda.')

@section('miga')
  <svg class="ico"><use href="#i-chevron"/></svg><span aria-current="page">Mensajes</span>
@endsection

@section('contenido')
  @php
    $puedeEditar = auth()->user()->puedeEditar();
    $pestanas = ['todos' => 'Todos', 'no-leidos' => 'Sin leer', 'leidos' => 'Leídos'];
    // La búsqueda se conserva al cambiar de pestaña.
    $conBusqueda = $buscar !== '' ? ['q' => $buscar] : [];
  @endphp

  {{-- ============ PESTAÑAS ============ --}}
  <nav class="pestanas" aria-label="Filtrar por lectura">
    @foreach ($pestanas as $clave => $nombre)
      <a href="{{ route('mensajes.index', $conBusqueda + ($clave === 'todos' ? [] : ['estado' => $clave])) }}"
         @class(['pestana', 'activo' => $estado === $clave]) @if ($estado === $clave) aria-current="page" @endif>
        {{ $nombre }} <b>{{ $conteos[$clave] }}</b>
      </a>
    @endforeach
  </nav>

  <section class="tarjeta" aria-label="Bandeja de mensajes">

    {{-- ============ BUSCADOR ============ --}}
    <form class="filtros" method="GET" action="{{ route('mensajes.index') }}" role="search">
      @if ($estado !== 'todos')
        <input type="hidden" name="estado" value="{{ $estado }}">
      @endif
      <div class="filtros__buscar">
        <svg class="ico" aria-hidden="true"><use href="#i-buscar"/></svg>
        <label class="sr-only" for="q">Buscar mensajes</label>
        <input id="q" name="q" type="search" value="{{ $buscar }}" placeholder="Buscar por nombre, correo o texto">
      </div>
      <button class="boton" type="submit">Buscar</button>
      @if ($buscar !== '')
        <a class="filtros__limpiar" href="{{ route('mensajes.index', $estado === 'todos' ? [] : ['estado' => $estado]) }}">Limpiar</a>
      @endif
    </form>

    {{-- ============ TABLA ============ --}}
    @if ($mensajes->isEmpty())
      <div class="vacio">
        <span class="vacio__icono"><svg class="ico ico--grande"><use href="#i-mensajes"/></svg></span>
        @if ($buscar !== '')
          <p class="vacio__titulo">Ningún mensaje coincide</p>
          <p>Prueba con otro nombre, correo o palabra.</p>
          <a class="boton" href="{{ route('mensajes.index') }}">Quitar la búsqueda</a>
        @elseif ($estado === 'no-leidos')
          <p class="vacio__titulo">Estás al día</p>
          <p>No hay mensajes sin leer.</p>
        @elseif ($estado === 'leidos')
          <p class="vacio__titulo">Todavía no leíste ningún mensaje</p>
        @else
          <p class="vacio__titulo">Todavía no hay mensajes</p>
          <p>Aparecerán aquí cuando alguien escriba desde la tienda.</p>
        @endif
      </div>
    @else
      <div class="tabla-caja">
        <table class="tabla">
          <thead>
            <tr>
              <th scope="col">De</th>
              <th scope="col">Mensaje</th>
              <th scope="col">Estado</th>
              <th scope="col">Recibido</th>
              <th scope="col"><span class="sr-only">Acciones</span></th>
            </tr>
          </thead>
          <tbody>
            @foreach ($mensajes as $mensaje)
              <tr>
                <td>
                  <span class="tabla__principal"><a href="{{ route('mensajes.show', $mensaje) }}">{{ $mensaje->nombre }}</a></span>
                  <span class="tabla__secundario">{{ $mensaje->correo }}</span>
                </td>
                <td @class(['negrita' => ! $mensaje->leido])>{{ \Illuminate\Support\Str::limit($mensaje->mensaje, 90) }}</td>
                <td>
                  <span @class(['insignia', 'insignia--naranja' => ! $mensaje->leido])>{{ $mensaje->leido ? 'Leído' : 'Sin leer' }}</span>
                </td>
                <td class="sin-salto">
                  <time datetime="{{ $mensaje->created_at->toIso8601String() }}" title="{{ $mensaje->created_at->format('d/m/Y H:i') }}">
                    {{ $mensaje->created_at->diffForHumans() }}
                  </time>
                </td>
                <td>
                  <div class="tabla__acciones">
                    <a class="boton boton--icono boton--fantasma" href="{{ route('mensajes.show', $mensaje) }}" title="Abrir" aria-label="Abrir el mensaje de {{ $mensaje->nombre }}">
                      <svg class="ico"><use href="#i-ojo"/></svg>
                    </a>
                    @if ($puedeEditar)
                      <form method="POST" action="{{ route('mensajes.leido', $mensaje) }}">
                        @csrf @method('PATCH')
                        <button class="boton boton--icono boton--fantasma" type="submit"
                                title="{{ $mensaje->leido ? 'Marcar como no leído' : 'Marcar como leído' }}"
                                aria-label="{{ $mensaje->leido ? 'Marcar como no leído' : 'Marcar como leído' }} el mensaje de {{ $mensaje->nombre }}">
                          <svg class="ico"><use href="#{{ $mensaje->leido ? 'i-mensajes' : 'i-check' }}"/></svg>
                        </button>
                      </form>
                      <form method="POST" action="{{ route('mensajes.destroy', $mensaje) }}"
                            data-confirmar="¿Eliminar el mensaje de {{ $mensaje->nombre }}? No se puede deshacer."
                            data-confirmar-boton="Sí, eliminar">
                        @csrf @method('DELETE')
                        <button class="boton boton--icono boton--fantasma boton--peligro" type="submit" title="Eliminar" aria-label="Eliminar el mensaje de {{ $mensaje->nombre }}">
                          <svg class="ico"><use href="#i-borrar"/></svg>
                        </button>
                      </form>
                    @endif
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      {{ $mensajes->links() }}
    @endif
  </section>
@endsection
