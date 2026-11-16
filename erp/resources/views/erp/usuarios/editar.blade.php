@extends('erp.layout')

@section('titulo', 'Editar a '.$usuario->name)
@section('descripcion', 'Cambia sus datos, su rol o desactiva su acceso.')

@section('miga')
  <svg class="ico"><use href="#i-chevron"/></svg><a href="{{ route('usuarios.index') }}">Usuarios</a><svg class="ico"><use href="#i-chevron"/></svg><span aria-current="page">Editar</span>
@endsection

@section('contenido')
  <form class="formulario" method="POST" action="{{ route('usuarios.update', $usuario) }}" novalidate>
    @csrf
    @method('PUT')
    @include('erp.usuarios._formulario')

    <div class="formulario__pie">
      <a class="boton" href="{{ route('usuarios.index') }}">Cancelar</a>
      <button class="boton boton--primario" type="submit">
        <svg class="ico"><use href="#i-check"/></svg> Guardar cambios
      </button>
    </div>
  </form>
@endsection
