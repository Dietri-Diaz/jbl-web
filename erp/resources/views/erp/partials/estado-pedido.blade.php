{{-- Insignia del estado de un pedido. Uso: @include('erp.partials.estado-pedido', ['estado' => $pedido->estado]) --}}
@php
  $clases = [
      'pendiente' => 'insignia--alerta',
      'confirmado' => 'insignia--info',
      'enviado' => 'insignia--naranja',
      'entregado' => 'insignia--ok',
      'cancelado' => 'insignia--error',
  ];
@endphp
<span class="insignia {{ $clases[$estado] ?? '' }}">{{ config("tienda.estados.$estado", $estado) }}</span>
