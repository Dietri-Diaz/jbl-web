@extends('erp.layout')

@section('titulo', 'Agregar parlante')
@section('miga')
  <a href="{{ route('parlantes.index') }}">Parlantes</a> / Nuevo
@endsection

@section('contenido')
  <form class="formulario formulario--panel" method="POST" action="{{ route('parlantes.store') }}" enctype="multipart/form-data" novalidate>
    @csrf
    @include('erp.parlantes._formulario')
    <div class="formulario__acciones">
      <button class="boton boton--primario" type="submit">Agregar al catálogo</button>
      <a class="boton" href="{{ route('parlantes.index') }}">Cancelar</a>
    </div>
  </form>
@endsection
