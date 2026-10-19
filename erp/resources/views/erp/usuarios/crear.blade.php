@extends('erp.layout')

@section('titulo', 'Agregar administrador')
@section('miga')
  <a href="{{ route('usuarios.index') }}">Administradores</a> / Nuevo
@endsection

@section('contenido')
  <form class="formulario formulario--panel" method="POST" action="{{ route('usuarios.store') }}" novalidate>
    @csrf
    @include('erp.usuarios._formulario')
    <div class="formulario__acciones">
      <button class="boton boton--primario" type="submit">Dar acceso</button>
      <a class="boton" href="{{ route('usuarios.index') }}">Cancelar</a>
    </div>
  </form>
@endsection
