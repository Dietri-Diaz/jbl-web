@extends('erp.layout')

@section('titulo', 'Inicio')

@section('acciones')
  @if (Route::has('parlantes.create'))
    <a class="boton boton--primario" href="{{ route('parlantes.create') }}">
      <svg class="ico"><use href="#i-mas"/></svg> Agregar parlante
    </a>
  @endif
@endsection

@section('contenido')
  <section class="cifras" aria-label="Resumen">
    <article class="cifra">
      <p class="cifra__etiqueta">Parlantes publicados</p>
      <p class="cifra__valor">{{ $publicados }}<span>/ {{ $totalParlantes }}</span></p>
      <p class="cifra__nota">Se muestran en la landing</p>
    </article>
    <article class="cifra {{ $noLeidos > 0 ? 'cifra--alerta' : '' }}">
      <p class="cifra__etiqueta">Mensajes sin leer</p>
      <p class="cifra__valor">{{ $noLeidos }}</p>
      <p class="cifra__nota">{{ $totalMensajes }} recibidos en total</p>
    </article>
    <article class="cifra">
      <p class="cifra__etiqueta">Administradores</p>
      <p class="cifra__valor">{{ $administradores }}</p>
      <p class="cifra__nota">Con acceso a este panel</p>
    </article>
  </section>

  <div class="rejilla">
    <section class="tarjeta">
      <header class="tarjeta__cabecera">
        <h2>Últimos mensajes</h2>
        @if (Route::has('mensajes.index'))
          <a href="{{ route('mensajes.index') }}">Ver todos</a>
        @endif
      </header>
      @forelse ($ultimosMensajes as $m)
        <article @class(['fila-mensaje', 'fila-mensaje--nuevo' => ! $m->leido])>
          <div>
            <p class="fila-mensaje__de">{{ $m->nombre }} <span>· {{ $m->correo }}</span></p>
            <p class="fila-mensaje__texto">{{ \Illuminate\Support\Str::limit($m->mensaje, 90) }}</p>
          </div>
          <time datetime="{{ $m->created_at->toIso8601String() }}">{{ $m->created_at->diffForHumans() }}</time>
        </article>
      @empty
        <p class="vacio">Todavía no llegan mensajes desde la landing.</p>
      @endforelse
    </section>

    <section class="tarjeta">
      <header class="tarjeta__cabecera">
        <h2>Catálogo en la landing</h2>
        @if (Route::has('parlantes.index'))
          <a href="{{ route('parlantes.index') }}">Administrar</a>
        @endif
      </header>
      <ol class="lista-catalogo">
        @forelse ($catalogo as $p)
          <li>
            <span class="lista-catalogo__orden">{{ $p->orden }}</span>
            <span class="lista-catalogo__nombre">{{ $p->nombre }}</span>
            <span @class(['insignia', 'insignia--ok' => $p->visible, 'insignia--apagada' => ! $p->visible])>
              {{ $p->visible ? 'Publicado' : 'Oculto' }}
            </span>
          </li>
        @empty
          <li class="vacio">El catálogo está vacío.</li>
        @endforelse
      </ol>
    </section>
  </div>
@endsection
