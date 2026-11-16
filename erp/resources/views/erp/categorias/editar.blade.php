@extends('erp.layout')

@section('titulo', 'Editar '.$categoria->nombre)
@section('descripcion', 'Los cambios se ven en la tienda apenas los guardas.')

@section('miga')
  <svg class="ico"><use href="#i-chevron"/></svg><a href="{{ route('categorias.index') }}">Categorías</a><svg class="ico"><use href="#i-chevron"/></svg><span aria-current="page">Editar</span>
@endsection

@section('contenido')
  <form class="formulario" method="POST" action="{{ route('categorias.update', $categoria) }}" novalidate>
    @csrf
    @method('PUT')
    @include('erp.categorias._formulario')

    <div class="formulario__pie">
      <a class="boton" href="{{ route('categorias.index') }}">Cancelar</a>
      <button class="boton boton--primario" type="submit">
        <svg class="ico"><use href="#i-check"/></svg> Guardar cambios
      </button>
    </div>
  </form>
@endsection
