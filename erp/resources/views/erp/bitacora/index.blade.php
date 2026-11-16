@extends('erp.layout')

@section('titulo', 'Bitácora')
@section('descripcion', 'Quién hizo qué y cuándo. Los registros no se pueden editar ni borrar.')

@section('miga')
  <svg class="ico"><use href="#i-chevron"/></svg><span aria-current="page">Bitácora</span>
@endsection

@section('contenido')
  @php
    $clasesAccion = [
        'ingresar' => 'insignia--info',
        'salir' => '',
        'crear' => 'insignia--ok',
        'editar' => 'insignia--alerta',
        'eliminar' => 'insignia--error',
        'estado' => 'insignia--naranja',
        'stock' => 'insignia--oscura',
    ];
  @endphp

  {{-- ============ INDICADORES ============ --}}
  <section class="kpis" aria-label="Indicadores de actividad">
    <div class="kpi">
      <span class="kpi__icono" aria-hidden="true"><svg class="ico"><use href="#i-bitacora"/></svg></span>
      <span class="kpi__etiqueta">Registros de hoy</span>
      <span class="kpi__valor">{{ $registrosHoy }}</span>
      <span class="kpi__nota">{{ today()->translatedFormat('d \d\e F') }}</span>
    </div>
    <div class="kpi">
      <span class="kpi__icono" aria-hidden="true"><svg class="ico"><use href="#i-usuarios"/></svg></span>
      <span class="kpi__etiqueta">Usuarios activos hoy</span>
      <span class="kpi__valor">{{ $usuariosHoy }}</span>
      <span class="kpi__nota">hicieron al menos una acción</span>
    </div>
    <div class="kpi kpi--oscuro">
      <span class="kpi__icono" aria-hidden="true"><svg class="ico"><use href="#i-buscar"/></svg></span>
      <span class="kpi__etiqueta">{{ $hayFiltros ? 'Registros encontrados' : 'Registros en total' }}</span>
      <span class="kpi__valor">{{ number_format($registros->total()) }}</span>
      <span class="kpi__nota">{{ $hayFiltros ? 'con los filtros aplicados' : 'desde el primer día' }}</span>
    </div>
  </section>

  <section class="tarjeta" aria-label="Registros de la bitácora">

    {{-- ============ FILTROS ============ --}}
    <form class="filtros" method="GET" action="{{ route('bitacora.index') }}" role="search">
      <div class="filtros__buscar">
        <svg class="ico" aria-hidden="true"><use href="#i-buscar"/></svg>
        <label class="sr-only" for="q">Buscar en la descripción</label>
        <input id="q" name="q" type="search" value="{{ $filtros['q'] ?? '' }}" placeholder="Buscar en la descripción">
      </div>

      <label class="sr-only" for="usuario">Usuario</label>
      <select id="usuario" name="usuario">
        <option value="">Todos los usuarios</option>
        @foreach ($usuarios as $nombre)
          <option value="{{ $nombre }}" @selected(($filtros['usuario'] ?? '') === $nombre)>{{ $nombre }}</option>
        @endforeach
      </select>

      <label class="sr-only" for="modulo">Módulo</label>
      <select id="modulo" name="modulo">
        <option value="">Todos los módulos</option>
        @foreach ($modulos as $modulo)
          <option value="{{ $modulo }}" @selected(($filtros['modulo'] ?? '') === $modulo)>{{ ucfirst($modulo) }}</option>
        @endforeach
      </select>

      <label class="sr-only" for="accion">Acción</label>
      <select id="accion" name="accion">
        <option value="">Todas las acciones</option>
        @foreach ($acciones as $clave => $nombre)
          <option value="{{ $clave }}" @selected(($filtros['accion'] ?? '') === $clave)>{{ $nombre }}</option>
        @endforeach
      </select>

      <label for="desde" class="tenue">Desde</label>
      <input id="desde" name="desde" type="date" value="{{ $filtros['desde'] ?? '' }}">
      <label for="hasta" class="tenue">Hasta</label>
      <input id="hasta" name="hasta" type="date" value="{{ $filtros['hasta'] ?? '' }}">

      <button class="boton" type="submit">Filtrar</button>
      @if ($hayFiltros)
        <a class="filtros__limpiar" href="{{ route('bitacora.index') }}">Limpiar</a>
      @endif
    </form>

    {{-- ============ TABLA ============ --}}
    @if ($registros->isEmpty())
      <div class="vacio">
        <span class="vacio__icono"><svg class="ico ico--grande"><use href="#i-bitacora"/></svg></span>
        @if ($hayFiltros)
          <p class="vacio__titulo">Ningún registro coincide</p>
          <p>Cambia la búsqueda, el usuario o las fechas.</p>
          <a class="boton" href="{{ route('bitacora.index') }}">Quitar filtros</a>
        @else
          <p class="vacio__titulo">La bitácora está vacía</p>
          <p>Se llenará sola a medida que se use el panel.</p>
        @endif
      </div>
    @else
      <div class="tabla-caja">
        <table class="tabla">
          <thead>
            <tr>
              <th scope="col">Fecha y hora</th>
              <th scope="col">Usuario</th>
              <th scope="col">Acción</th>
              <th scope="col">Módulo</th>
              <th scope="col">Descripción</th>
              <th scope="col">IP</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($registros as $registro)
              <tr>
                <td class="sin-salto">
                  <time datetime="{{ $registro->created_at->toIso8601String() }}">
                    <span class="tabla__principal">{{ $registro->created_at->format('d/m/Y') }}</span>
                    <span class="tabla__secundario">{{ $registro->created_at->format('H:i:s') }}</span>
                  </time>
                </td>
                <td>{{ $registro->usuario }}</td>
                <td><span class="insignia {{ $clasesAccion[$registro->accion] ?? '' }}">{{ $registro->nombreAccion() }}</span></td>
                <td>{{ ucfirst($registro->modulo) }}</td>
                <td>{{ $registro->descripcion }}</td>
                <td class="mono tenue">{{ $registro->ip ?? '—' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      {{ $registros->links() }}
    @endif
  </section>
@endsection
