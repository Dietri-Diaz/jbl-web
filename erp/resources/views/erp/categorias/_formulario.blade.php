{{-- Campos compartidos por "Nueva categoría" y "Editar categoría". --}}

{{-- ============ COLUMNA PRINCIPAL ============ --}}
<div class="formulario__columna">
  <section class="tarjeta" aria-labelledby="f-informacion">
    <header class="tarjeta__cabecera">
      <h2 class="tarjeta__titulo" id="f-informacion">Información</h2>
    </header>
    <div class="tarjeta__cuerpo pila">
      <div class="campo">
        <label for="nombre">Nombre</label>
        <input id="nombre" name="nombre" type="text" maxlength="60" required
               value="{{ old('nombre', $categoria->nombre) }}" placeholder="Parlantes portátiles"
               @error('nombre') aria-invalid="true" aria-describedby="nombre-error" @enderror>
        @error('nombre') <p class="campo__error" id="nombre-error">{{ $message }}</p> @enderror
      </div>

      <div class="campo">
        <label for="descripcion">Descripción <span class="campo__opcional">(opcional)</span></label>
        <textarea id="descripcion" name="descripcion" maxlength="200" rows="3"
                  placeholder="Una línea que explique qué hay en esta categoría."
                  aria-describedby="descripcion-ayuda @error('descripcion') descripcion-error @enderror"
                  @error('descripcion') aria-invalid="true" @enderror>{{ old('descripcion', $categoria->descripcion) }}</textarea>
        <p class="campo__ayuda" id="descripcion-ayuda">Hasta 200 caracteres.</p>
        @error('descripcion') <p class="campo__error" id="descripcion-error">{{ $message }}</p> @enderror
      </div>
    </div>
  </section>
</div>

{{-- ============ COLUMNA LATERAL ============ --}}
<div class="formulario__columna">
  <section class="tarjeta" aria-labelledby="f-publicacion">
    <header class="tarjeta__cabecera">
      <h2 class="tarjeta__titulo" id="f-publicacion">Publicación</h2>
    </header>
    <div class="tarjeta__cuerpo pila">
      {{-- El campo oculto envía 0 cuando el interruptor está apagado. --}}
      <label class="interruptor">
        <input type="hidden" name="visible" value="0">
        <input type="checkbox" name="visible" value="1" @checked(old('visible', $categoria->visible))>
        <span class="interruptor__palanca" aria-hidden="true"></span>
        <span class="interruptor__texto">
          <b>Visible en la tienda</b>
          <span>Si la ocultas, sus productos tampoco se muestran.</span>
        </span>
      </label>

      <div class="campo">
        <label for="orden">Orden <span class="campo__opcional">(opcional)</span></label>
        <input id="orden" name="orden" type="number" min="0" max="999" inputmode="numeric"
               value="{{ old('orden', $categoria->orden) }}"
               aria-describedby="orden-ayuda @error('orden') orden-error @enderror"
               @error('orden') aria-invalid="true" @enderror>
        <p class="campo__ayuda" id="orden-ayuda">El número más bajo aparece primero.</p>
        @error('orden') <p class="campo__error" id="orden-error">{{ $message }}</p> @enderror
      </div>
    </div>
    @if ($categoria->exists)
      <div class="tarjeta__pie">
        <a href="{{ route('productos.index', ['categoria' => $categoria->id]) }}">
          Ver sus {{ $categoria->productos_count }} {{ $categoria->productos_count === 1 ? 'producto' : 'productos' }}
        </a>
      </div>
    @endif
  </section>
</div>
