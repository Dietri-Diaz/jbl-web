{{-- Campos compartidos por "Nuevo producto" y "Editar producto". --}}

{{-- ============ COLUMNA PRINCIPAL ============ --}}
<div class="formulario__columna">

  <section class="tarjeta" aria-labelledby="f-informacion">
    <header class="tarjeta__cabecera">
      <h2 class="tarjeta__titulo" id="f-informacion">Información</h2>
    </header>
    <div class="tarjeta__cuerpo campos">
      <div class="campo campo--ancho">
        <label for="nombre">Nombre</label>
        <input id="nombre" name="nombre" type="text" maxlength="80" required
               value="{{ old('nombre', $producto->nombre) }}" placeholder="JBL Flip 6"
               @error('nombre') aria-invalid="true" aria-describedby="nombre-error" @enderror>
        @error('nombre') <p class="campo__error" id="nombre-error">{{ $message }}</p> @enderror
      </div>

      <div class="campo">
        <label for="categoria_id">Categoría</label>
        <select id="categoria_id" name="categoria_id" required
                @error('categoria_id') aria-invalid="true" aria-describedby="categoria_id-error" @enderror>
          <option value="">Elige una categoría</option>
          @foreach ($categorias as $categoria)
            <option value="{{ $categoria->id }}" @selected(old('categoria_id', $producto->categoria_id) == $categoria->id)>
              {{ $categoria->nombre }}{{ $categoria->visible ? '' : ' (oculta)' }}
            </option>
          @endforeach
        </select>
        @error('categoria_id') <p class="campo__error" id="categoria_id-error">{{ $message }}</p> @enderror
      </div>

      <div class="campo">
        <label for="tipo">Tipo</label>
        <input id="tipo" name="tipo" type="text" maxlength="40" required
               value="{{ old('tipo', $producto->tipo) }}" placeholder="Portátil"
               @error('tipo') aria-invalid="true" aria-describedby="tipo-error" @enderror>
        @error('tipo') <p class="campo__error" id="tipo-error">{{ $message }}</p> @enderror
      </div>

      <div class="campo">
        <label for="sku">SKU <span class="campo__opcional">(opcional)</span></label>
        <input id="sku" name="sku" type="text" maxlength="30" class="mono"
               value="{{ old('sku', $producto->sku) }}" placeholder="JBL-FLIP6-NEG" aria-describedby="sku-ayuda @error('sku') sku-error @enderror"
               @error('sku') aria-invalid="true" @enderror>
        <p class="campo__ayuda" id="sku-ayuda">Código interno del producto. No se puede repetir.</p>
        @error('sku') <p class="campo__error" id="sku-error">{{ $message }}</p> @enderror
      </div>

      <div class="campo">
        <label for="color">Color <span class="campo__opcional">(opcional)</span></label>
        <input id="color" name="color" type="text" maxlength="30"
               value="{{ old('color', $producto->color) }}" placeholder="Negro"
               @error('color') aria-invalid="true" aria-describedby="color-error" @enderror>
        @error('color') <p class="campo__error" id="color-error">{{ $message }}</p> @enderror
      </div>

      <div class="campo campo--ancho">
        <label for="descripcion">Descripción <span class="campo__opcional">(opcional)</span></label>
        <textarea id="descripcion" name="descripcion" maxlength="2000" rows="4"
                  placeholder="Lo que el cliente lee en la ficha del producto."
                  @error('descripcion') aria-invalid="true" aria-describedby="descripcion-error" @enderror>{{ old('descripcion', $producto->descripcion) }}</textarea>
        @error('descripcion') <p class="campo__error" id="descripcion-error">{{ $message }}</p> @enderror
      </div>
    </div>
  </section>

  <section class="tarjeta" aria-labelledby="f-precio">
    <header class="tarjeta__cabecera">
      <h2 class="tarjeta__titulo" id="f-precio">Precio</h2>
      <span class="tarjeta__nota">En soles, con IGV</span>
    </header>
    <div class="tarjeta__cuerpo campos">
      <div class="campo">
        <label for="precio">Precio</label>
        <div class="con-prefijo">
          <span aria-hidden="true">S/</span>
          <input id="precio" name="precio" type="number" min="0.01" max="999999.99" step="0.01" inputmode="decimal" required
                 value="{{ old('precio', $producto->precio) }}"
                 @error('precio') aria-invalid="true" aria-describedby="precio-error" @enderror>
        </div>
        @error('precio') <p class="campo__error" id="precio-error">{{ $message }}</p> @enderror
      </div>

      <div class="campo">
        <label for="precio_oferta">Precio de oferta <span class="campo__opcional">(opcional)</span></label>
        <div class="con-prefijo">
          <span aria-hidden="true">S/</span>
          <input id="precio_oferta" name="precio_oferta" type="number" min="0.01" step="0.01" inputmode="decimal"
                 value="{{ old('precio_oferta', $producto->precio_oferta) }}" aria-describedby="precio_oferta-ayuda @error('precio_oferta') precio_oferta-error @enderror"
                 @error('precio_oferta') aria-invalid="true" @enderror>
        </div>
        <p class="campo__ayuda" id="precio_oferta-ayuda">Debe ser menor que el precio. Déjalo vacío si no está en oferta.</p>
        @error('precio_oferta') <p class="campo__error" id="precio_oferta-error">{{ $message }}</p> @enderror
      </div>
    </div>
  </section>

  <section class="tarjeta" aria-labelledby="f-ficha">
    <header class="tarjeta__cabecera">
      <h2 class="tarjeta__titulo" id="f-ficha">Ficha técnica</h2>
      <span class="tarjeta__nota">Todo es opcional</span>
    </header>
    <div class="tarjeta__cuerpo campos campos--3">
      <div class="campo">
        <label for="potencia_w">Potencia (W)</label>
        <input id="potencia_w" name="potencia_w" type="number" min="1" max="2000" inputmode="numeric"
               value="{{ old('potencia_w', $producto->potencia_w) }}"
               @error('potencia_w') aria-invalid="true" aria-describedby="potencia_w-error" @enderror>
        @error('potencia_w') <p class="campo__error" id="potencia_w-error">{{ $message }}</p> @enderror
      </div>

      <div class="campo">
        <label for="bateria_h">Batería (horas)</label>
        <input id="bateria_h" name="bateria_h" type="number" min="0" max="100" inputmode="numeric"
               value="{{ old('bateria_h', $producto->bateria_h) }}"
               @error('bateria_h') aria-invalid="true" aria-describedby="bateria_h-error" @enderror>
        @error('bateria_h') <p class="campo__error" id="bateria_h-error">{{ $message }}</p> @enderror
      </div>

      <div class="campo">
        <label for="resistencia">Resistencia al agua</label>
        <select id="resistencia" name="resistencia"
                @error('resistencia') aria-invalid="true" aria-describedby="resistencia-error" @enderror>
          <option value="">No aplica</option>
          @foreach ($resistencias as $resistencia)
            <option value="{{ $resistencia }}" @selected(old('resistencia', $producto->resistencia) === $resistencia)>{{ $resistencia }}</option>
          @endforeach
        </select>
        @error('resistencia') <p class="campo__error" id="resistencia-error">{{ $message }}</p> @enderror
      </div>
    </div>
  </section>
</div>

{{-- ============ COLUMNA LATERAL ============ --}}
<div class="formulario__columna">

  <section class="tarjeta" aria-labelledby="f-foto">
    <header class="tarjeta__cabecera">
      <h2 class="tarjeta__titulo" id="f-foto">Foto</h2>
    </header>
    <div class="tarjeta__cuerpo pila">
      <div class="campo">
        <div class="foto-subida">
          <div class="foto-subida__vista" id="foto-vista">
            @if ($producto->foto)
              <img src="{{ $producto->urlFoto() }}" alt="{{ $producto->foto_alt }}">
            @else
              <span>Aquí verás la foto que elijas</span>
            @endif
          </div>
          <label class="campo__etiqueta" for="foto">{{ $producto->exists ? 'Cambiar la foto' : 'Foto del producto' }}
            @if ($producto->exists) <span class="campo__opcional">(opcional)</span> @endif
          </label>
          <input id="foto" name="foto" type="file" accept="image/jpeg,image/png,image/webp" data-vista="#foto-vista"
                 {{ $producto->exists ? '' : 'required' }} aria-describedby="foto-ayuda @error('foto') foto-error @enderror"
                 @error('foto') aria-invalid="true" @enderror>
        </div>
        <p class="campo__ayuda" id="foto-ayuda">JPG, PNG o WEBP de hasta 4 MB. Se ve mejor en horizontal 4:3.</p>
        @error('foto') <p class="campo__error" id="foto-error">{{ $message }}</p> @enderror
      </div>

      <div class="campo">
        <label for="foto_alt">Texto alternativo</label>
        <input id="foto_alt" name="foto_alt" type="text" maxlength="160" required
               value="{{ old('foto_alt', $producto->foto_alt) }}" placeholder="Parlante JBL Flip 6 negro visto de frente"
               aria-describedby="foto_alt-ayuda @error('foto_alt') foto_alt-error @enderror"
               @error('foto_alt') aria-invalid="true" @enderror>
        <p class="campo__ayuda" id="foto_alt-ayuda">Describe la foto para quien usa lector de pantalla.</p>
        @error('foto_alt') <p class="campo__error" id="foto_alt-error">{{ $message }}</p> @enderror
      </div>
    </div>
  </section>

  <section class="tarjeta" aria-labelledby="f-publicacion">
    <header class="tarjeta__cabecera">
      <h2 class="tarjeta__titulo" id="f-publicacion">Publicación</h2>
    </header>
    <div class="tarjeta__cuerpo pila">
      {{-- El campo oculto envía 0 cuando el interruptor está apagado. --}}
      <label class="interruptor">
        <input type="hidden" name="visible" value="0">
        <input type="checkbox" name="visible" value="1" @checked(old('visible', $producto->visible))>
        <span class="interruptor__palanca" aria-hidden="true"></span>
        <span class="interruptor__texto">
          <b>Publicado</b>
          <span>Se muestra y se puede comprar en la tienda.</span>
        </span>
      </label>

      <label class="interruptor">
        <input type="hidden" name="destacado" value="0">
        <input type="checkbox" name="destacado" value="1" @checked(old('destacado', $producto->destacado))>
        <span class="interruptor__palanca" aria-hidden="true"></span>
        <span class="interruptor__texto">
          <b>Destacado</b>
          <span>Aparece entre los recomendados de la portada.</span>
        </span>
      </label>

      <div class="campo">
        <label for="sello">Sello <span class="campo__opcional">(opcional)</span></label>
        <input id="sello" name="sello" type="text" maxlength="30"
               value="{{ old('sello', $producto->sello) }}" placeholder="El más potente"
               aria-describedby="sello-ayuda @error('sello') sello-error @enderror"
               @error('sello') aria-invalid="true" @enderror>
        <p class="campo__ayuda" id="sello-ayuda">Etiqueta corta que resalta la tarjeta del producto.</p>
        @error('sello') <p class="campo__error" id="sello-error">{{ $message }}</p> @enderror
      </div>

      <div class="campo">
        <label for="orden">Orden <span class="campo__opcional">(opcional)</span></label>
        <input id="orden" name="orden" type="number" min="0" max="999" inputmode="numeric"
               value="{{ old('orden', $producto->orden) }}"
               aria-describedby="orden-ayuda @error('orden') orden-error @enderror"
               @error('orden') aria-invalid="true" @enderror>
        <p class="campo__ayuda" id="orden-ayuda">El número más bajo aparece primero.</p>
        @error('orden') <p class="campo__error" id="orden-error">{{ $message }}</p> @enderror
      </div>
    </div>
  </section>

  <section class="tarjeta" aria-labelledby="f-inventario">
    <header class="tarjeta__cabecera">
      <h2 class="tarjeta__titulo" id="f-inventario">{{ $producto->exists ? 'Inventario' : 'Inventario inicial' }}</h2>
    </header>
    <div class="tarjeta__cuerpo pila">
      @if ($producto->exists)
        {{-- El stock no se edita aquí: cada cambio debe quedar como movimiento. --}}
        <dl class="detalle detalle--1">
          <div>
            <dt>Stock actual</dt>
            <dd>@include('erp.partials.nivel', ['producto' => $producto])</dd>
          </div>
        </dl>
      @else
        <div class="campo">
          <label for="stock_inicial">Stock inicial <span class="campo__opcional">(opcional)</span></label>
          <input id="stock_inicial" name="stock_inicial" type="number" min="0" max="99999" inputmode="numeric"
                 value="{{ old('stock_inicial', 0) }}"
                 aria-describedby="stock_inicial-ayuda @error('stock_inicial') stock_inicial-error @enderror"
                 @error('stock_inicial') aria-invalid="true" @enderror>
          <p class="campo__ayuda" id="stock_inicial-ayuda">Unidades con las que empieza. Se registra como una entrada en el inventario.</p>
          @error('stock_inicial') <p class="campo__error" id="stock_inicial-error">{{ $message }}</p> @enderror
        </div>
      @endif

      <div class="campo">
        <label for="stock_minimo">Stock mínimo</label>
        <input id="stock_minimo" name="stock_minimo" type="number" min="0" max="9999" inputmode="numeric" required
               value="{{ old('stock_minimo', $producto->stock_minimo) }}"
               aria-describedby="stock_minimo-ayuda @error('stock_minimo') stock_minimo-error @enderror"
               @error('stock_minimo') aria-invalid="true" @enderror>
        <p class="campo__ayuda" id="stock_minimo-ayuda">Al llegar a esta cantidad, el panel avisa que hay que reponer.</p>
        @error('stock_minimo') <p class="campo__error" id="stock_minimo-error">{{ $message }}</p> @enderror
      </div>
    </div>
    @if ($producto->exists)
      <div class="tarjeta__pie">
        <a href="{{ route('inventario.ajustar', $producto) }}">Ajustar stock</a>
      </div>
    @endif
  </section>
</div>
