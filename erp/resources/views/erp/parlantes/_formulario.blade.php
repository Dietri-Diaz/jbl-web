{{-- Campos compartidos por "Agregar parlante" y "Editar parlante". --}}
<div class="formulario__rejilla">
  <div class="campo">
    <label for="nombre">Nombre</label>
    <input id="nombre" name="nombre" type="text" maxlength="80" required
           value="{{ old('nombre', $parlante->nombre) }}" placeholder="JBL Flip 6" @class(['con-error' => $errors->has('nombre')])>
    @error('nombre') <p class="campo__error">{{ $message }}</p> @enderror
  </div>

  <div class="campo">
    <label for="tipo">Tipo</label>
    <input id="tipo" name="tipo" type="text" maxlength="40" required
           value="{{ old('tipo', $parlante->tipo) }}" placeholder="Portátil" @class(['con-error' => $errors->has('tipo')])>
    @error('tipo') <p class="campo__error">{{ $message }}</p> @enderror
  </div>

  <div class="campo">
    <label for="potencia_w">Potencia (W)</label>
    <input id="potencia_w" name="potencia_w" type="number" min="1" max="2000" required
           value="{{ old('potencia_w', $parlante->potencia_w) }}" @class(['con-error' => $errors->has('potencia_w')])>
    @error('potencia_w') <p class="campo__error">{{ $message }}</p> @enderror
  </div>

  <div class="campo">
    <label for="bateria_h">Batería (horas)</label>
    <input id="bateria_h" name="bateria_h" type="number" min="0" max="100" required
           value="{{ old('bateria_h', $parlante->bateria_h) }}" @class(['con-error' => $errors->has('bateria_h')])>
    @error('bateria_h') <p class="campo__error">{{ $message }}</p> @enderror
  </div>

  <div class="campo">
    <label for="resistencia">Resistencia al agua</label>
    <select id="resistencia" name="resistencia" required @class(['con-error' => $errors->has('resistencia')])>
      @foreach ($resistencias as $r)
        <option value="{{ $r }}" @selected(old('resistencia', $parlante->resistencia) === $r)>{{ $r }}</option>
      @endforeach
    </select>
    @error('resistencia') <p class="campo__error">{{ $message }}</p> @enderror
  </div>

  <div class="campo">
    <label for="orden">Orden en la landing</label>
    <input id="orden" name="orden" type="number" min="0" max="999"
           value="{{ old('orden', $parlante->orden) }}" @class(['con-error' => $errors->has('orden')])>
    <p class="campo__ayuda">El número más bajo aparece primero.</p>
    @error('orden') <p class="campo__error">{{ $message }}</p> @enderror
  </div>

  <div class="campo campo--ancho">
    <label for="foto">Foto {{ $parlante->exists ? '(opcional, solo si la cambias)' : '' }}</label>
    @if ($parlante->foto)
      <div class="foto-actual">
        <img src="{{ $parlante->urlFoto() }}" alt="{{ $parlante->foto_alt }}" width="120" height="90">
        <p class="campo__ayuda">Foto actual. Si subes otra, esta se reemplaza.</p>
      </div>
    @endif
    <input id="foto" name="foto" type="file" accept="image/jpeg,image/png,image/webp" @class(['con-error' => $errors->has('foto')])>
    <p class="campo__ayuda">JPG, PNG o WEBP de hasta 4 MB. Se ve mejor en formato horizontal 4:3.</p>
    @error('foto') <p class="campo__error">{{ $message }}</p> @enderror
  </div>

  <div class="campo campo--ancho">
    <label for="foto_alt">Texto alternativo de la foto</label>
    <input id="foto_alt" name="foto_alt" type="text" maxlength="160" required
           value="{{ old('foto_alt', $parlante->foto_alt) }}" placeholder="Parlante JBL Flip 6 azul sobre una mesa"
           @class(['con-error' => $errors->has('foto_alt')])>
    <p class="campo__ayuda">Describe la foto para quien usa lector de pantalla.</p>
    @error('foto_alt') <p class="campo__error">{{ $message }}</p> @enderror
  </div>

  <div class="campo">
    <label for="sello">Sello destacado (opcional)</label>
    <input id="sello" name="sello" type="text" maxlength="30"
           value="{{ old('sello', $parlante->sello) }}" placeholder="El más potente" @class(['con-error' => $errors->has('sello')])>
    <p class="campo__ayuda">Si lo llenas, la tarjeta se resalta en la landing.</p>
    @error('sello') <p class="campo__error">{{ $message }}</p> @enderror
  </div>

  <div class="campo">
    <span class="campo__etiqueta">Publicación</span>
    <label class="casilla">
      <input type="checkbox" name="visible" value="1" @checked(old('visible', $parlante->visible))>
      Mostrar en la landing
    </label>
  </div>
</div>
