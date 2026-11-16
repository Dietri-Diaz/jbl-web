@extends('erp.layout')

@section('titulo', 'Nueva categoría')
@section('descripcion', 'Después podrás asignarle productos desde el catálogo.')

@section('miga')
  <svg class="ico"><use href="#i-chevron"/></svg><a href="{{ route('categorias.index') }}">Categorías</a><svg class="ico"><use href="#i-chevron"/></svg><span aria-current="page">Nueva</span>
@endsection

@section('contenido')
  <form class="formulario" method="POST" action="{{ route('categorias.store') }}" novalidate>
    @csrf
    @include('erp.categorias._formulario')

    <div class="formulario__pie">
      <a class="boton" href="{{ route('categorias.index') }}">Cancelar</a>
      <button class="boton boton--primario" type="submit">
        <svg class="ico"><use href="#i-check"/></svg> Guardar categoría
      </button>
    </div>
  </form>
@endsection
