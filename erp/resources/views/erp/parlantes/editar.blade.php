@extends('erp.layout')

@section('titulo', 'Editar '.$parlante->nombre)
@section('miga')
  <a href="{{ route('parlantes.index') }}">Parlantes</a> / Editar
@endsection

@section('contenido')
  <form class="formulario formulario--panel" method="POST" action="{{ route('parlantes.update', $parlante) }}" enctype="multipart/form-data" novalidate>
    @csrf
    @method('PUT')
    @include('erp.parlantes._formulario')
    <div class="formulario__acciones">
      <button class="boton boton--primario" type="submit">Guardar cambios</button>
      <a class="boton" href="{{ route('parlantes.index') }}">Cancelar</a>
    </div>
  </form>
@endsection
