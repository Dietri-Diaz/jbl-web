<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('titulo', 'Panel') · JBL ERP</title>
  <link rel="icon" href="{{ asset('img/logo-jbl.svg') }}" type="image/svg+xml">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/erp.css') }}">
</head>
<body>
@include('erp.partials.iconos')

<div class="erp">

  {{-- ============ MENÚ LATERAL ============ --}}
  <aside class="lateral">
    <a class="lateral__marca" href="{{ route('panel') }}">
      <img src="{{ asset('img/logo-jbl.svg') }}" alt="JBL" width="44" height="31">
      <span>ERP</span>
    </a>

    <nav class="lateral__menu" aria-label="Módulos del ERP">
      <a href="{{ route('panel') }}" @class(['activo' => request()->routeIs('panel')])>
        <svg class="ico"><use href="#i-inicio"/></svg> Inicio
      </a>
      @if (Route::has('parlantes.index'))
        <a href="{{ route('parlantes.index') }}" @class(['activo' => request()->routeIs('parlantes.*')])>
          <svg class="ico"><use href="#i-parlante"/></svg> Parlantes
        </a>
      @endif
      @if (Route::has('mensajes.index'))
        <a href="{{ route('mensajes.index') }}" @class(['activo' => request()->routeIs('mensajes.*')])>
          <svg class="ico"><use href="#i-mensajes"/></svg> Mensajes
          @if ($mensajesNoLeidos > 0)
            <span class="contador" aria-label="{{ $mensajesNoLeidos }} sin leer">{{ $mensajesNoLeidos }}</span>
          @endif
        </a>
      @endif
      @if (Route::has('usuarios.index'))
        <a href="{{ route('usuarios.index') }}" @class(['activo' => request()->routeIs('usuarios.*')])>
          <svg class="ico"><use href="#i-usuarios"/></svg> Administradores
        </a>
      @endif
    </nav>

    <div class="lateral__pie">
      <a href="{{ config('app.landing_url') }}" target="_blank" rel="noopener">
        <svg class="ico"><use href="#i-externo"/></svg> Ver la landing
      </a>
      <div class="lateral__usuario">
        <span class="lateral__nombre">{{ auth()->user()->name }}</span>
        <form method="POST" action="{{ route('salir') }}">
          @csrf
          <button type="submit" class="lateral__salir">
            <svg class="ico"><use href="#i-salir"/></svg> Salir
          </button>
        </form>
      </div>
    </div>
  </aside>

  {{-- ============ CONTENIDO ============ --}}
  <main class="contenido">
    <header class="cabecera">
      <div>
        @hasSection('miga')
          <p class="cabecera__miga">@yield('miga')</p>
        @endif
        <h1 class="cabecera__titulo">@yield('titulo', 'Panel')</h1>
      </div>
      <div class="cabecera__acciones">@yield('acciones')</div>
    </header>

    @if (session('ok'))
      <div class="aviso aviso--ok" role="status">
        <svg class="ico"><use href="#i-check"/></svg> {{ session('ok') }}
      </div>
    @endif

    @if (session('error'))
      <div class="aviso aviso--error" role="alert">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
      <div class="aviso aviso--error" role="alert">
        Revisa los campos marcados: {{ $errors->count() === 1 ? 'hay 1 error' : 'hay '.$errors->count().' errores' }}.
      </div>
    @endif

    @yield('contenido')
  </main>

</div>
</body>
</html>
