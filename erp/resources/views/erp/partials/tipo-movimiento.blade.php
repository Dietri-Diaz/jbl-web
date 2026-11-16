{{-- Insignia del tipo de movimiento. Uso: @include('erp.partials.tipo-movimiento', ['movimiento' => $movimiento]) --}}
@php
  $clases = [
      'entrada' => 'insignia--ok',
      'salida' => 'insignia--alerta',
      'ajuste' => 'insignia--info',
      'venta' => 'insignia--naranja',
      'devolucion' => 'insignia--oscura',
  ];
@endphp
<span class="insignia {{ $clases[$movimiento->tipo] ?? '' }}">{{ $movimiento->nombreTipo() }}</span>
