@extends('erp.layout')

@section('titulo', 'Parlantes')

@section('acciones')
  <a class="boton boton--primario" href="{{ route('parlantes.create') }}">
    <svg class="ico"><use href="#i-mas"/></svg> Agregar parlante
  </a>
@endsection

@section('contenido')
  <form class="filtros" method="GET" action="{{ route('parlantes.index') }}" role="search">
    <div class="filtros__buscar">
      <svg class="ico"><use href="#i-buscar"/></svg>
      <label class="sr-only" for="q">Buscar</label>
      <input id="q" name="q" type="search" value="{{ $buscar }}" placeholder="Buscar por nombre o tipo">
    </div>
    <label class="sr-only" for="estado">Estado</label>
    <select id="estado" name="estado" onchange="this.form.submit()">
      <option value="todos" @selected($estado === 'todos')>Todos</option>
      <option value="publicados" @selected($estado === 'publicados')>Publicados</option>
      <option value="ocultos" @selected($estado === 'ocultos')>Ocultos</option>
    </select>
    <button class="boton" type="submit">Filtrar</button>
  </form>

  <div class="tabla-caja">
    <table class="tabla">
      <thead>
        <tr>
          <th scope="col">Foto</th>
          <th scope="col">Parlante</th>
          <th scope="col">Ficha técnica</th>
          <th scope="col">Orden</th>
          <th scope="col">Estado</th>
          <th scope="col"><span class="sr-only">Acciones</span></th>
        </tr>
      </thead>
      <tbody>
        @forelse ($parlantes as $p)
          <tr>
            <td>
              @if ($p->foto)
                <img class="tabla__foto" src="{{ $p->urlFoto() }}" alt="{{ $p->foto_alt }}" width="64" height="48" loading="lazy">
              @else
                <span class="tabla__foto" aria-hidden="true"></span>
              @endif
            </td>
            <td>
              <span class="tabla__principal">{{ $p->nombre }}</span>
              <span class="tabla__secundario">{{ $p->tipo }}@if ($p->sello) · {{ $p->sello }}@endif</span>
            </td>
            <td class="specs-cortas">{{ $p->potencia_w }} W · {{ $p->bateria_h }} h · {{ $p->resistencia }}</td>
            <td>{{ $p->orden }}</td>
            <td>
              <span @class(['insignia', 'insignia--ok' => $p->visible, 'insignia--apagada' => ! $p->visible])>
                {{ $p->visible ? 'Publicado' : 'Oculto' }}
              </span>
            </td>
            <td>
              <div class="tabla__acciones">
                <form method="POST" action="{{ route('parlantes.visible', $p) }}">
                  @csrf @method('PATCH')
                  <button class="boton boton--icono" type="submit"
                          title="{{ $p->visible ? 'Ocultar en la landing' : 'Publicar en la landing' }}"
                          aria-label="{{ $p->visible ? 'Ocultar' : 'Publicar' }} {{ $p->nombre }}">
                    <svg class="ico"><use href="#{{ $p->visible ? 'i-ojo-no' : 'i-ojo' }}"/></svg>
                  </button>
                </form>
                <a class="boton boton--icono" href="{{ route('parlantes.edit', $p) }}" title="Editar" aria-label="Editar {{ $p->nombre }}">
                  <svg class="ico"><use href="#i-editar"/></svg>
                </a>
                <form method="POST" action="{{ route('parlantes.destroy', $p) }}"
                      onsubmit="return confirm('¿Eliminar el {{ $p->nombre }} del catálogo? No se puede deshacer.')">
                  @csrf @method('DELETE')
                  <button class="boton boton--icono boton--peligro" type="submit" title="Eliminar" aria-label="Eliminar {{ $p->nombre }}">
                    <svg class="ico"><use href="#i-borrar"/></svg>
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="vacio">No hay parlantes que coincidan con la búsqueda.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endsection
