{{-- Campos compartidos por "Agregar administrador" y "Editar administrador". --}}
<div class="formulario__rejilla">
  <div class="campo">
    <label for="name">Nombre</label>
    <input id="name" name="name" type="text" maxlength="80" required autocomplete="name"
           value="{{ old('name', $usuario->name) }}" @class(['con-error' => $errors->has('name')])>
    @error('name') <p class="campo__error">{{ $message }}</p> @enderror
  </div>

  <div class="campo">
    <label for="email">Correo</label>
    <input id="email" name="email" type="email" maxlength="120" required autocomplete="email"
           value="{{ old('email', $usuario->email) }}" @class(['con-error' => $errors->has('email')])>
    @error('email') <p class="campo__error">{{ $message }}</p> @enderror
  </div>

  <div class="campo">
    <label for="password">{{ $usuario->exists ? 'Nueva contraseña (opcional)' : 'Contraseña' }}</label>
    <input id="password" name="password" type="password" autocomplete="new-password" {{ $usuario->exists ? '' : 'required' }}
           @class(['con-error' => $errors->has('password')])>
    <p class="campo__ayuda">Mínimo 8 caracteres.{{ $usuario->exists ? ' Déjala vacía para no cambiarla.' : '' }}</p>
    @error('password') <p class="campo__error">{{ $message }}</p> @enderror
  </div>

  <div class="campo">
    <label for="password_confirmation">Repite la contraseña</label>
    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password">
  </div>
</div>
