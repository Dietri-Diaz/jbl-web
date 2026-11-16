<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex">
  <title>@yield('titulo', 'Panel') · JBL ERP</title>
  <link rel="icon" href="{{ asset('img/logo-jbl.svg') }}" type="image/svg+xml">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/erp.css') }}?v={{ filemtime(public_path('css/erp.css')) }}">
</head>
<body>
@include('erp.partials.iconos')

@php($yo = auth()->user())

<div class="erp" id="erp">

  {{-- ============ MENÚ LATERAL ============ --}}
  <aside class="lateral" id="lateral" aria-label="Menú principal">
    <a class="lateral__marca" href="{{ route('panel') }}">
      <img src="{{ asset('img/logo-jbl.svg') }}" alt="JBL" width="44" height="31">
      <span>ERP <small>Tienda en línea</small></span>
    </a>

    <nav class="lateral__menu" aria-label="Módulos">
      <a href="{{ route('panel') }}" @class(['activo' => request()->routeIs('panel')])>
        <svg class="ico"><use href="#i-inicio"/></svg> Inicio
      </a>

      <p class="lateral__grupo">Ventas</p>
      <a href="{{ route('pedidos.index') }}" @class(['activo' => request()->routeIs('pedidos.*')])>
        <svg class="ico"><use href="#i-pedidos"/></svg> Pedidos
        @if ($menu['pedidos'] > 0)
          <span class="contador" aria-label="{{ $menu['pedidos'] }} pendientes">{{ $menu['pedidos'] }}</span>
        @endif
      </a>
      <a href="{{ route('mensajes.index') }}" @class(['activo' => request()->routeIs('mensajes.*')])>
        <svg class="ico"><use href="#i-mensajes"/></svg> Mensajes
        @if ($menu['mensajes'] > 0)
          <span class="contador" aria-label="{{ $menu['mensajes'] }} sin leer">{{ $menu['mensajes'] }}</span>
        @endif
      </a>

      <p class="lateral__grupo">Catálogo</p>
      <a href="{{ route('productos.index') }}" @class(['activo' => request()->routeIs('productos.*')])>
        <svg class="ico"><use href="#i-parlante"/></svg> Productos
      </a>
      <a href="{{ route('categorias.index') }}" @class(['activo' => request()->routeIs('categorias.*')])>
        <svg class="ico"><use href="#i-categorias"/></svg> Categorías
      </a>

      <p class="lateral__grupo">Inventario</p>
      <a href="{{ route('inventario.index') }}" @class(['activo' => request()->routeIs('inventario.index', 'inventario.ajustar')])>
        <svg class="ico"><use href="#i-inventario"/></svg> Existencias
        @if ($menu['stock'] > 0)
          <span class="contador contador--alerta" aria-label="{{ $menu['stock'] }} con stock bajo">{{ $menu['stock'] }}</span>
        @endif
      </a>
      <a href="{{ route('inventario.movimientos') }}" @class(['activo' => request()->routeIs('inventario.movimientos')])>
        <svg class="ico"><use href="#i-movimientos"/></svg> Movimientos
      </a>

      @if ($yo->esAdmin())
        <p class="lateral__grupo">Administración</p>
        <a href="{{ route('usuarios.index') }}" @class(['activo' => request()->routeIs('usuarios.*')])>
          <svg class="ico"><use href="#i-usuarios"/></svg> Usuarios
        </a>
        <a href="{{ route('bitacora.index') }}" @class(['activo' => request()->routeIs('bitacora.*')])>
          <svg class="ico"><use href="#i-bitacora"/></svg> Bitácora
        </a>
      @endif
    </nav>

    <div class="lateral__pie">
      <a href="{{ config('app.landing_url') }}" target="_blank" rel="noopener">
        <svg class="ico"><use href="#i-externo"/></svg> Ver la tienda
      </a>
    </div>
  </aside>

  {{-- Velo para cerrar el menú en pantallas pequeñas --}}
  <button class="velo" type="button" data-menu-cerrar aria-label="Cerrar el menú"></button>

  <div class="principal">

    {{-- ============ BARRA SUPERIOR ============ --}}
    <header class="barra">
      <button class="boton boton--icono barra__menu" type="button" data-menu-abrir aria-controls="lateral" aria-expanded="false" aria-label="Abrir el menú">
        <svg class="ico"><use href="#i-menu"/></svg>
      </button>

      <nav class="miga" aria-label="Ruta">
        <a href="{{ route('panel') }}">Inicio</a>
        @hasSection('miga')
          @yield('miga')
        @endif
      </nav>

      <div class="barra__derecha">
        <details class="usuario">
          <summary aria-label="Menú de {{ $yo->name }}">
            <span class="avatar" aria-hidden="true">{{ $yo->iniciales() }}</span>
            <span class="usuario__nombre">{{ $yo->name }}<span class="usuario__rol">{{ $yo->nombreRol() }}</span></span>
            <svg class="ico" aria-hidden="true"><use href="#i-abajo"/></svg>
          </summary>
          <div class="usuario__menu">
            <a href="{{ route('perfil.editar') }}"><svg class="ico"><use href="#i-usuario"/></svg> Mi perfil</a>
            <form method="POST" action="{{ route('salir') }}">
              @csrf
              <button type="submit"><svg class="ico"><use href="#i-salir"/></svg> Cerrar sesión</button>
            </form>
          </div>
        </details>
      </div>
    </header>

    {{-- ============ CONTENIDO ============ --}}
    <main class="contenido" id="contenido">
      <header class="pagina__cabecera">
        <div>
          <h1 class="pagina__titulo">@yield('titulo', 'Panel')</h1>
          @hasSection('descripcion')
            <p class="pagina__sub">@yield('descripcion')</p>
          @endif
        </div>
        @hasSection('acciones')
          <div class="pagina__acciones">@yield('acciones')</div>
        @endif
      </header>

      @if ($errors->any())
        <div class="nota nota--error" role="alert">
          <svg class="ico"><use href="#i-alerta"/></svg>
          Revisa los campos marcados: {{ $errors->count() === 1 ? 'hay 1 error' : 'hay '.$errors->count().' errores' }}.
        </div>
      @endif

      @yield('contenido')
    </main>
  </div>
</div>

{{-- ============ AVISOS (éxito y error de la última acción) ============ --}}
<div class="avisos" aria-live="polite">
  @if (session('ok'))
    <div class="aviso aviso--ok" role="status" data-aviso>
      <svg class="ico"><use href="#i-check"/></svg>
      <span>{{ session('ok') }}</span>
      <button class="aviso__cerrar" type="button" data-aviso-cerrar aria-label="Cerrar el aviso">&times;</button>
    </div>
  @endif
  @if (session('error'))
    <div class="aviso aviso--error" role="alert" data-aviso data-fijo>
      <svg class="ico"><use href="#i-alerta"/></svg>
      <span>{{ session('error') }}</span>
      <button class="aviso__cerrar" type="button" data-aviso-cerrar aria-label="Cerrar el aviso">&times;</button>
    </div>
  @endif
</div>

{{-- ============ DIÁLOGO DE CONFIRMACIÓN ============
     Cualquier formulario con data-confirmar="pregunta" pasa por aquí antes de enviarse. --}}
<dialog class="dialogo" id="confirmar" aria-labelledby="confirmar-titulo">
  <div class="dialogo__cuerpo">
    <h2 class="dialogo__titulo" id="confirmar-titulo">¿Confirmas esta acción?</h2>
    <p class="dialogo__texto" id="confirmar-texto"></p>
  </div>
  <div class="dialogo__pie">
    <button class="boton" type="button" data-dialogo-cancelar>Cancelar</button>
    <button class="boton boton--oscuro" type="button" data-dialogo-aceptar>Sí, continuar</button>
  </div>
</dialog>

@stack('scripts')
<script src="{{ asset('js/erp.js') }}?v={{ filemtime(public_path('js/erp.js')) }}"></script>
</body>
</html>
