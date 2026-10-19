@extends('erp.layout')

@section('titulo', 'Administradores')

@section('acciones')
  <a class="boton boton--primario" href="{{ route('usuarios.create') }}">
    <svg class="ico"><use href="#i-mas"/></svg> Agregar administrador
  </a>
@endsection

@section('contenido')
  <div class="tabla-caja">
    <table class="tabla">
      <thead>
        <tr>
          <th scope="col">Nombre</th>
          <th scope="col">Correo</th>
          <th scope="col">Desde</th>
          <th scope="col"><span class="sr-only">Acciones</span></th>
        </tr>
      </thead>
      <tbody>
        @foreach ($usuarios as $u)
          <tr>
            <td>
              <span class="tabla__principal">{{ $u->name }}</span>
              @if ($u->is(auth()->user()))
                <span class="insignia insignia--nuevo">Tú</span>
              @endif
            </td>
            <td>{{ $u->email }}</td>
            <td>{{ $u->created_at?->format('d/m/Y') }}</td>
            <td>
              <div class="tabla__acciones">
                <a class="boton boton--icono" href="{{ route('usuarios.edit', $u) }}" title="Editar" aria-label="Editar a {{ $u->name }}">
                  <svg class="ico"><use href="#i-editar"/></svg>
                </a>
                @unless ($u->is(auth()->user()))
                  <form method="POST" action="{{ route('usuarios.destroy', $u) }}"
                        onsubmit="return confirm('¿Quitar el acceso de {{ $u->name }} al panel?')">
                    @csrf @method('DELETE')
                    <button class="boton boton--icono boton--peligro" type="submit" title="Quitar acceso" aria-label="Quitar el acceso de {{ $u->name }}">
                      <svg class="ico"><use href="#i-borrar"/></svg>
                    </button>
                  </form>
                @endunless
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@endsection
