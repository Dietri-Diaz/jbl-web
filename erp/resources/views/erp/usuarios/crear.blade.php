@extends('erp.layout')

@section('titulo', 'Nuevo usuario')
@section('descripcion', 'Crea la cuenta y entrégale su correo y contraseña. Podrá cambiarla desde su perfil.')

@section('miga')
  <svg class="ico"><use href="#i-chevron"/></svg><a href="{{ route('usuarios.index') }}">Usuarios</a><svg class="ico"><use href="#i-chevron"/></svg><span aria-current="page">Nuevo</span>
@endsection

@section('contenido')
  <form class="formulario" method="POST" action="{{ route('usuarios.store') }}" novalidate>
    @csrf
    @include('erp.usuarios._formulario')

    <div class="formulario__pie">
      <a class="boton" href="{{ route('usuarios.index') }}">Cancelar</a>
      <button class="boton boton--primario" type="submit">
        <svg class="ico"><use href="#i-check"/></svg> Crear usuario
      </button>
    </div>
  </form>
@endsection
