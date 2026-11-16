@extends('erp.layout')

@section('titulo', 'Nuevo producto')
@section('descripcion', 'Completa la ficha. Si lo dejas publicado, se podrá comprar en la tienda apenas lo guardes.')

@section('miga')
  <svg class="ico"><use href="#i-chevron"/></svg><a href="{{ route('productos.index') }}">Productos</a><svg class="ico"><use href="#i-chevron"/></svg><span aria-current="page">Nuevo</span>
@endsection

@section('contenido')
  <form class="formulario" method="POST" action="{{ route('productos.store') }}" enctype="multipart/form-data" novalidate>
    @csrf
    @include('erp.productos._formulario')

    <div class="formulario__pie">
      <a class="boton" href="{{ route('productos.index') }}">Cancelar</a>
      <button class="boton boton--primario" type="submit">
        <svg class="ico"><use href="#i-check"/></svg> Guardar producto
      </button>
    </div>
  </form>
@endsection
