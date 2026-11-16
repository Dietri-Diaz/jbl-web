@extends('erp.layout')

@section('titulo', 'Mi perfil')
@section('descripcion', 'Tus datos para ingresar al panel.')

@section('miga')
  <svg class="ico"><use href="#i-chevron"/></svg><span aria-current="page">Mi perfil</span>
@endsection

@section('contenido')
  <form class="formulario" method="POST" action="{{ route('perfil.actualizar') }}" novalidate>
    @csrf
    @method('PUT')

    {{-- ============ COLUMNA PRINCIPAL ============ --}}
    <div class="formulario__columna">
      <section class="tarjeta" aria-labelledby="f-datos">
        <header class="tarjeta__cabecera">
          <h2 class="tarjeta__titulo" id="f-datos">Mis datos</h2>
        </header>
        <div class="tarjeta__cuerpo campos">
          <div class="campo">
            <label for="name">Nombre</label>
            <input id="name" name="name" type="text" maxlength="80" required autocomplete="name"
                   value="{{ old('name', $usuario->name) }}"
                   @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
            @error('name') <p class="campo__error" id="name-error">{{ $message }}</p> @enderror
          </div>

          <div class="campo">
            <label for="email">Correo</label>
            <input id="email" name="email" type="email" maxlength="120" required autocomplete="email"
                   value="{{ old('email', $usuario->email) }}"
                   @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
            @error('email') <p class="campo__error" id="email-error">{{ $message }}</p> @enderror
          </div>
        </div>
      </section>

      <section class="tarjeta" aria-labelledby="f-clave">
        <header class="tarjeta__cabecera">
          <h2 class="tarjeta__titulo" id="f-clave">Cambiar contraseña</h2>
          <span class="tarjeta__nota">Opcional: déjalo vacío para no cambiarla</span>
        </header>
        <div class="tarjeta__cuerpo campos">
          <div class="campo campo--ancho">
            <label for="password_actual">Contraseña actual</label>
            <input id="password_actual" name="password_actual" type="password" autocomplete="current-password"
                   aria-describedby="password_actual-ayuda @error('password_actual') password_actual-error @enderror"
                   @error('password_actual') aria-invalid="true" @enderror>
            <p class="campo__ayuda" id="password_actual-ayuda">Solo se pide si vas a poner una nueva.</p>
            @error('password_actual') <p class="campo__error" id="password_actual-error">{{ $message }}</p> @enderror
          </div>

          <div class="campo">
            <label for="password">Nueva contraseña</label>
            <input id="password" name="password" type="password" autocomplete="new-password"
                   aria-describedby="password-ayuda @error('password') password-error @enderror"
                   @error('password') aria-invalid="true" @enderror>
            <p class="campo__ayuda" id="password-ayuda">Mínimo 8 caracteres.</p>
            @error('password') <p class="campo__error" id="password-error">{{ $message }}</p> @enderror
          </div>

          <div class="campo">
            <label for="password_confirmation">Repite la nueva contraseña</label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password">
          </div>
        </div>
      </section>
    </div>

    {{-- ============ COLUMNA LATERAL ============ --}}
    <div class="formulario__columna">
      <section class="tarjeta" aria-labelledby="f-cuenta">
        <header class="tarjeta__cabecera">
          <h2 class="tarjeta__titulo" id="f-cuenta">Mi cuenta</h2>
        </header>
        <ul class="lista">
          <li>
            <span class="avatar avatar--grande" aria-hidden="true">{{ $usuario->iniciales() }}</span>
            <span class="lista__texto">
              <span class="lista__principal">{{ $usuario->name }}</span>
              <span class="lista__secundario">{{ $usuario->email }}</span>
            </span>
          </li>
        </ul>
        <div class="tarjeta__cuerpo">
          <dl class="detalle detalle--1">
            <div>
              <dt>Rol</dt>
              <dd>{{ $usuario->nombreRol() }}</dd>
            </div>
            <div>
              <dt>Último ingreso</dt>
              <dd>{{ $usuario->ultimo_ingreso ? $usuario->ultimo_ingreso->format('d/m/Y H:i') : 'Sin registro' }}</dd>
            </div>
          </dl>
        </div>
        <div class="tarjeta__pie tenue">El rol solo lo cambia un administrador.</div>
      </section>
    </div>

    <div class="formulario__pie">
      <a class="boton" href="{{ route('panel') }}">Cancelar</a>
      <button class="boton boton--primario" type="submit">
        <svg class="ico"><use href="#i-check"/></svg> Guardar cambios
      </button>
    </div>
  </form>
@endsection
