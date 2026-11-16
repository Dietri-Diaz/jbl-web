{{-- Barra de nivel de stock. Uso: @include('erp.partials.nivel', ['producto' => $producto]) --}}
@php
  $nivel = $producto->estadoStock();
  // La barra se llena al llegar al triple del mínimo; agotado se pinta completa en rojo.
  $tope = max($producto->stock_minimo * 3, 1);
  $ancho = $nivel === 'agotado' ? 100 : min(100, (int) round($producto->stock / $tope * 100));
  $textos = ['agotado' => 'Agotado', 'bajo' => 'Stock bajo', 'disponible' => 'Disponible'];
@endphp
<div @class(['nivel', 'nivel--bajo' => $nivel === 'bajo', 'nivel--agotado' => $nivel === 'agotado']) title="{{ $textos[$nivel] }}">
  <span class="nivel__barra" aria-hidden="true"><i style="width: {{ $ancho }}%"></i></span>
  <span class="nivel__numero">{{ $producto->stock }}</span>
  <span class="sr-only">unidades · {{ $textos[$nivel] }}</span>
</div>
