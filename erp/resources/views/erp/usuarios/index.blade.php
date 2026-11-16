@extends('erp.layout')

@section('titulo', 'Usuarios')
@section('descripcion', 'Quién entra al panel y qué puede hacer cada uno.')

@section('miga')
  <svg class="ico"><use href="#i-chevron"/></svg><span aria-current="page">Usuarios</span>
@endsection

@section('acciones')
  <a class="boton boton--primario" href="{{ route('usuarios.create') }}">
    <svg class="ico"><use href="#i-mas"/></svg> Nuevo usuario
  </a>
@endsection

@section('contenido')
  @php
    $yo = auth()->user();
    $clasesRol = ['admin' => 'insignia--oscura', 'editor' => 'insignia--info', 'lector' => ''];
  @endphp

  {{-- ============ INDICADORES ============ --}}
  <section class="kpis" aria-label="Indicadores de usuarios">
    <div class="kpi">
      <span class="kpi__icono" aria-hidden="true"><svg class="ico"><use href="#i-usuarios"/></svg></span>
      <span class="kpi__etiqueta">Usuarios</span>
      <span class="kpi__valor">{{ $total }}</span>
      <span class="kpi__nota">cuentas creadas</span>
    </div>
    <div class="kpi">
      <span class="kpi__icono" aria-hidden="true"><svg class="ico"><use href="#i-check"/></svg></span>
      <span class="kpi__etiqueta">Activos</span>
      <span class="kpi__valor">{{ $activos }}</span>
      <span class="kpi__nota">{{ $total - $activos }} {{ $total - $activos === 1 ? 'desactivado' : 'desactivados' }}</span>
    </div>
    <div class="kpi">
      <span class="kpi__icono" aria-hidden="true"><svg class="ico"><use href="#i-candado"/></svg></span>
      <span class="kpi__etiqueta">Administradores</span>
      <span class="kpi__valor">{{ $administradores }}</span>
      <span class="kpi__nota">activos · siempre debe quedar uno</span>
    </div>
  </section>

  <section class="tarjeta" aria-label="Listado de usuarios">

    {{-- ============ FILTROS ============ --}}
    <form class="filtros" method="GET" action="{{ route('usuarios.index') }}" role="search">
      <div class="filtros__buscar">
        <svg class="ico" aria-hidden="true"><use href="#i-buscar"/></svg>
        <label class="sr-only" for="q">Buscar usuarios</label>
        <input id="q" name="q" type="search" value="{{ $filtros['q'] ?? '' }}" placeholder="Buscar por nombre o correo">
      </div>

      <label class="sr-only" for="rol">Rol</label>
      <select id="rol" name="rol">
        <option value="">Todos los roles</option>
        @foreach ($roles as $clave => $nombre)
          <option value="{{ $clave }}" @selected(($filtros['rol'] ?? '') === $clave)>{{ $nombre }}</option>
        @endforeach
      </select>

      <label class="sr-only" for="estado">Estado</label>
      <select id="estado" name="estado">
        <option value="">Activos e inactivos</option>
        <option value="activos" @selected(($filtros['estado'] ?? '') === 'activos')>Activos</option>
        <option value="inactivos" @selected(($filtros['estado'] ?? '') === 'inactivos')>Desactivados</option>
      </select>

      <button class="boton" type="submit">Filtrar</button>
      @if ($hayFiltros)
        <a class="filtros__limpiar" href="{{ route('usuarios.index') }}">Limpiar</a>
      @endif
    </form>

    {{-- ============ TABLA ============ --}}
    @if ($usuarios->isEmpty())
      <div class="vacio">
        <span class="vacio__icono"><svg class="ico ico--grande"><use href="#i-usuarios"/></svg></span>
        <p class="vacio__titulo">Ningún usuario coincide</p>
        <p>Prueba con otra búsqueda o quita los filtros.</p>
        <a class="boton" href="{{ route('usuarios.index') }}">Quitar filtros</a>
      </div>
    @else
      <div class="tabla-caja">
        <table class="tabla">
          <thead>
            <tr>
              <th scope="col">Usuario</th>
              <th scope="col">Rol</th>
              <th scope="col">Estado</th>
              <th scope="col">Último ingreso</th>
              <th scope="col"><span class="sr-only">Acciones</span></th>
            </tr>
          </thead>
          <tbody>
            @foreach ($usuarios as $usuario)
              <tr>
                <td>
                  <div class="tabla__producto">
                    <span class="avatar" aria-hidden="true">{{ $usuario->iniciales() }}</span>
                    <div>
                      <span class="tabla__principal">
                        <a href="{{ route('usuarios.edit', $usuario) }}">{{ $usuario->name }}</a>
                        @if ($usuario->is($yo))
                          <span class="insignia insignia--naranja insignia--sin-punto">Tú</span>
                        @endif
                      </span>
                      <span class="tabla__secundario">{{ $usuario->email }}</span>
                    </div>
                  </div>
                </td>
                <td><span class="insignia insignia--sin-punto {{ $clasesRol[$usuario->rol] ?? '' }}">{{ $usuario->nombreRol() }}</span></td>
                <td><span @class(['insignia', 'insignia--ok' => $usuario->activo, 'insignia--error' => ! $usuario->activo])>{{ $usuario->activo ? 'Activo' : 'Desactivado' }}</span></td>
                <td>
                  @if ($usuario->ultimo_ingreso)
                    <time datetime="{{ $usuario->ultimo_ingreso->toIso8601String() }}" title="{{ $usuario->ultimo_ingreso->format('d/m/Y H:i') }}">{{ $usuario->ultimo_ingreso->diffForHumans() }}</time>
                  @else
                    <span class="tenue">Nunca ingresó</span>
                  @endif
                </td>
                <td>
                  <div class="tabla__acciones">
                    <a class="boton boton--icono boton--fantasma" href="{{ route('usuarios.edit', $usuario) }}" title="Editar" aria-label="Editar a {{ $usuario->name }}">
                      <svg class="ico"><use href="#i-editar"/></svg>
                    </a>
                    {{-- Nadie se elimina a sí mismo: en la propia fila no hay botón. --}}
                    @unless ($usuario->is($yo))
                      <form method="POST" action="{{ route('usuarios.destroy', $usuario) }}"
                            data-confirmar="¿Eliminar la cuenta de {{ $usuario->name }}? Ya no podrá ingresar al panel. Si solo quieres pausar su acceso, desactívala."
                            data-confirmar-boton="Sí, eliminar">
                        @csrf @method('DELETE')
                        <button class="boton boton--icono boton--fantasma boton--peligro" type="submit" title="Eliminar" aria-label="Eliminar a {{ $usuario->name }}">
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

      {{ $usuarios->links() }}
    @endif
  </section>
@endsection
