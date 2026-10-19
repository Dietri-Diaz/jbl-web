<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ingresar · JBL ERP</title>
  <link rel="icon" href="{{ asset('img/logo-jbl.svg') }}" type="image/svg+xml">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/erp.css') }}">
</head>
<body class="acceso">

<main class="acceso__caja">
  <img class="acceso__logo" src="{{ asset('img/logo-jbl.svg') }}" alt="JBL" width="72" height="51">
  <h1 class="acceso__titulo">Panel del catálogo</h1>
  <p class="acceso__texto">Administra los parlantes y los mensajes de la landing.</p>

  @if (session('ok'))
    <div class="aviso aviso--ok" role="status">{{ session('ok') }}</div>
  @endif

  <form method="POST" action="{{ route('login.ingresar') }}" class="formulario" novalidate>
    @csrf
    <div class="campo">
      <label for="email">Correo</label>
      <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus
             @class(['con-error' => $errors->has('email')]) aria-describedby="email-error">
      @error('email') <p class="campo__error" id="email-error">{{ $message }}</p> @enderror
    </div>
    <div class="campo">
      <label for="password">Contraseña</label>
      <input id="password" name="password" type="password" autocomplete="current-password" required
             @class(['con-error' => $errors->has('password')])>
      @error('password') <p class="campo__error">{{ $message }}</p> @enderror
    </div>
    <label class="casilla">
      <input type="checkbox" name="recordar" value="1"> Mantener la sesión iniciada
    </label>
    <button type="submit" class="boton boton--primario boton--ancho">Ingresar</button>
  </form>
</main>

</body>
</html>
