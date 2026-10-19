@extends('erp.layout')

@section('titulo', 'Mensajes')

@section('contenido')
  <form class="filtros" method="GET" action="{{ route('mensajes.index') }}" role="search">
    <div class="filtros__buscar">
      <svg class="ico"><use href="#i-buscar"/></svg>
      <label class="sr-only" for="q">Buscar</label>
      <input id="q" name="q" type="search" value="{{ $buscar }}" placeholder="Buscar por nombre, correo o texto">
    </div>
    <label class="sr-only" for="estado">Estado</label>
    <select id="estado" name="estado" onchange="this.form.submit()">
      <option value="todos" @selected($estado === 'todos')>Todos</option>
      <option value="no-leidos" @selected($estado === 'no-leidos')>Sin leer</option>
      <option value="leidos" @selected($estado === 'leidos')>Leídos</option>
    </select>
    <button class="boton" type="submit">Filtrar</button>
  </form>

  <div class="tabla-caja">
    <table class="tabla">
      <thead>
        <tr>
          <th scope="col">Estado</th>
          <th scope="col">De</th>
          <th scope="col">Mensaje</th>
          <th scope="col">Recibido</th>
          <th scope="col"><span class="sr-only">Acciones</span></th>
        </tr>
      </thead>
      <tbody>
        @forelse ($mensajes as $m)
          <tr @class(['no-leido' => ! $m->leido])>
            <td>
              <span @class(['insignia', 'insignia--nuevo' => ! $m->leido, 'insignia--apagada' => $m->leido])>
                {{ $m->leido ? 'Leído' : 'Nuevo' }}
              </span>
            </td>
            <td>
              <span class="tabla__principal">{{ $m->nombre }}</span>
              <span class="tabla__secundario">{{ $m->correo }}</span>
            </td>
            <td>{{ \Illuminate\Support\Str::limit($m->mensaje, 80) }}</td>
            <td>
              <time datetime="{{ $m->created_at->toIso8601String() }}" title="{{ $m->created_at->format('d/m/Y H:i') }}">
                {{ $m->created_at->diffForHumans() }}
              </time>
            </td>
            <td>
              <div class="tabla__acciones">
                <a class="boton boton--chico" href="{{ route('mensajes.show', $m) }}">Abrir</a>
                <form method="POST" action="{{ route('mensajes.destroy', $m) }}"
                      onsubmit="return confirm('¿Eliminar el mensaje de {{ $m->nombre }}? No se puede deshacer.')">
                  @csrf @method('DELETE')
                  <button class="boton boton--icono boton--peligro" type="submit" title="Eliminar" aria-label="Eliminar el mensaje de {{ $m->nombre }}">
                    <svg class="ico"><use href="#i-borrar"/></svg>
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="vacio">No hay mensajes{{ $estado === 'no-leidos' ? ' sin leer' : '' }}.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{ $mensajes->links() }}
@endsection
