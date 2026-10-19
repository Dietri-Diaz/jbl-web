@extends('erp.layout')

@section('titulo', 'Editar a '.$usuario->name)
@section('miga')
  <a href="{{ route('usuarios.index') }}">Administradores</a> / Editar
@endsection

@section('contenido')
  <form class="formulario formulario--panel" method="POST" action="{{ route('usuarios.update', $usuario) }}" novalidate>
    @csrf
    @method('PUT')
    @include('erp.usuarios._formulario')
    <div class="formulario__acciones">
      <button class="boton boton--primario" type="submit">Guardar cambios</button>
      <a class="boton" href="{{ route('usuarios.index') }}">Cancelar</a>
    </div>
  </form>
@endsection
