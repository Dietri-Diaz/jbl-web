<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex">
  <title>Ingresar · JBL ERP</title>
  <link rel="icon" href="{{ asset('img/logo-jbl.svg') }}" type="image/svg+xml">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/erp.css') }}?v={{ filemtime(public_path('css/erp.css')) }}">
</head>
<body>

<div class="login">

  {{-- Lado de marca --}}
  <aside class="login__marca">
    <img class="login__logo" src="{{ asset('img/logo-jbl.svg') }}" alt="JBL" width="72" height="51">
    <p class="login__lema">El sonido <em>se administra</em> aquí</p>
    <p class="login__pie">ERP de la tienda en línea · Catálogo, pedidos e inventario</p>
  </aside>

  {{-- Formulario --}}
  <main class="login__caja">
    <form method="POST" action="{{ route('login.ingresar') }}" class="login__form" novalidate>
      @csrf
      <h1 class="login__titulo">Ingresar al panel</h1>
      <p class="login__sub">Usa el correo y la contraseña de tu cuenta.</p>

      @if (session('ok'))
        <div class="nota" role="status">{{ session('ok') }}</div>
      @endif

      <div class="campo">
        <label for="email">Correo</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus
               @if ($errors->has('email')) aria-invalid="true" aria-describedby="email-error" @endif>
        @error('email') <p class="campo__error" id="email-error">{{ $message }}</p> @enderror
      </div>

      <div class="campo">
        <label for="password">Contraseña</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required
               @if ($errors->has('password')) aria-invalid="true" aria-describedby="password-error" @endif>
        @error('password') <p class="campo__error" id="password-error">{{ $message }}</p> @enderror
      </div>

      <label class="login__recordar">
        <input type="checkbox" name="recordar" value="1"> Mantener la sesión iniciada
      </label>

      <button type="submit" class="boton boton--primario">Ingresar</button>

      @if (app()->environment('local'))
        <p class="login__demo">
          Cuenta de demostración: <code>admin@jbl.pe</code> · <code>admin123</code>
        </p>
      @endif
    </form>
  </main>

</div>

</body>
</html>
