{{-- Campos compartidos por "Nuevo usuario" y "Editar usuario". --}}
@php
  // Qué puede hacer cada rol, explicado para quien da el acceso.
  $explicacion = [
      'admin' => 'Todo: además gestiona usuarios y ve la bitácora.',
      'editor' => 'Catálogo, inventario, pedidos y mensajes.',
      'lector' => 'Puede ver todo, pero no modificar nada.',
  ];
  $esMiCuenta = $usuario->exists && $usuario->is(auth()->user());
@endphp

{{-- ============ COLUMNA PRINCIPAL ============ --}}
<div class="formulario__columna">
  <section class="tarjeta" aria-labelledby="f-datos">
    <header class="tarjeta__cabecera">
      <h2 class="tarjeta__titulo" id="f-datos">Datos de la cuenta</h2>
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
               value="{{ old('email', $usuario->email) }}" aria-describedby="email-ayuda @error('email') email-error @enderror"
               @error('email') aria-invalid="true" @enderror>
        <p class="campo__ayuda" id="email-ayuda">Con este correo ingresa al panel.</p>
        @error('email') <p class="campo__error" id="email-error">{{ $message }}</p> @enderror
      </div>

      <div class="campo">
        <label for="password">{{ $usuario->exists ? 'Nueva contraseña' : 'Contraseña' }}
          @if ($usuario->exists) <span class="campo__opcional">(opcional)</span> @endif
        </label>
        <input id="password" name="password" type="password" autocomplete="new-password" {{ $usuario->exists ? '' : 'required' }}
               aria-describedby="password-ayuda @error('password') password-error @enderror"
               @error('password') aria-invalid="true" @enderror>
        <p class="campo__ayuda" id="password-ayuda">Mínimo 8 caracteres.{{ $usuario->exists ? ' Déjala vacía para no cambiarla.' : '' }}</p>
        @error('password') <p class="campo__error" id="password-error">{{ $message }}</p> @enderror
      </div>

      <div class="campo">
        <label for="password_confirmation">Repite la contraseña</label>
        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" {{ $usuario->exists ? '' : 'required' }}>
      </div>
    </div>
  </section>

  <section class="tarjeta" aria-labelledby="f-rol">
    <header class="tarjeta__cabecera">
      <h2 class="tarjeta__titulo" id="f-rol">Rol</h2>
      <span class="tarjeta__nota">Define qué puede hacer en el panel</span>
    </header>
    <div class="tarjeta__cuerpo">
      <div class="campo" role="radiogroup" aria-labelledby="f-rol" @error('rol') aria-describedby="rol-error" @enderror>
        <div class="opciones opciones--fila">
          @foreach (config('tienda.roles') as $clave => $nombre)
            <label class="opcion">
              <input type="radio" name="rol" value="{{ $clave }}" @checked(old('rol', $usuario->rol) === $clave)>
              <span class="opcion__caja">
                <b>{{ $nombre }}</b>
                <span>{{ $explicacion[$clave] ?? '' }}</span>
              </span>
            </label>
          @endforeach
        </div>
        @if ($esMiCuenta)
          <p class="campo__ayuda">Es tu propia cuenta: no puedes quitarte el rol de administrador.</p>
        @endif
        @error('rol') <p class="campo__error" id="rol-error">{{ $message }}</p> @enderror
      </div>
    </div>
  </section>
</div>

{{-- ============ COLUMNA LATERAL ============ --}}
<div class="formulario__columna">
  <section class="tarjeta" aria-labelledby="f-acceso">
    <header class="tarjeta__cabecera">
      <h2 class="tarjeta__titulo" id="f-acceso">Acceso</h2>
    </header>
    <div class="tarjeta__cuerpo pila">
      {{-- El campo oculto envía 0 cuando el interruptor está apagado. --}}
      <label class="interruptor">
        <input type="hidden" name="activo" value="0">
        <input type="checkbox" name="activo" value="1" @checked(old('activo', $usuario->activo))
               @error('activo') aria-invalid="true" aria-describedby="activo-error" @enderror>
        <span class="interruptor__palanca" aria-hidden="true"></span>
        <span class="interruptor__texto">
          <b>Cuenta activa</b>
          <span>Si la desactivas, no puede ingresar, pero su historial se conserva.</span>
        </span>
      </label>
      @error('activo') <p class="campo__error" id="activo-error">{{ $message }}</p> @enderror

      @if ($usuario->exists)
        <dl class="detalle detalle--1">
          <div>
            <dt>Último ingreso</dt>
            <dd>{{ $usuario->ultimo_ingreso ? $usuario->ultimo_ingreso->format('d/m/Y H:i').' ('.$usuario->ultimo_ingreso->diffForHumans().')' : 'Nunca ingresó' }}</dd>
          </div>
          <div>
            <dt>Cuenta creada</dt>
            <dd>{{ $usuario->created_at?->format('d/m/Y') ?? '—' }}</dd>
          </div>
        </dl>
      @endif
    </div>
  </section>
</div>
