@extends('erp.layout')

@section('titulo', 'Editar '.$producto->nombre)
@section('descripcion', 'Los cambios se ven en la tienda apenas los guardas.')

@section('miga')
  <svg class="ico"><use href="#i-chevron"/></svg><a href="{{ route('productos.index') }}">Productos</a><svg class="ico"><use href="#i-chevron"/></svg><span aria-current="page">Editar</span>
@endsection

@section('contenido')
  <form class="formulario" method="POST" action="{{ route('productos.update', $producto) }}" enctype="multipart/form-data" novalidate>
    @csrf
    @method('PUT')
    @include('erp.productos._formulario')

    <div class="formulario__pie">
      <a class="boton" href="{{ route('productos.index') }}">Cancelar</a>
      <button class="boton boton--primario" type="submit">
        <svg class="ico"><use href="#i-check"/></svg> Guardar cambios
      </button>
    </div>
  </form>
@endsection
