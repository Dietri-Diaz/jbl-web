<?php

// Reglas de la tienda. La landing las recibe por la API y el panel las usa
// para calcular los pedidos, así los dos lados siempre coinciden.
return [

    'moneda' => 'S/',

    // Costo del envío a domicilio y monto desde el que sale gratis.
    'envio' => 12.00,
    'envio_gratis_desde' => 299.00,

    // Máximo de unidades de un mismo producto por pedido.
    'maximo_por_producto' => 5,

    'entregas' => [
        'delivery' => 'Envío a domicilio (Lima Metropolitana)',
        'recojo' => 'Recojo en tienda',
    ],

    'pagos' => [
        'contraentrega' => 'Pago contra entrega',
        'yape' => 'Yape o Plin',
        'transferencia' => 'Transferencia bancaria',
    ],

    // Estados de un pedido, en el orden en que avanza.
    'estados' => [
        'pendiente' => 'Pendiente',
        'confirmado' => 'Confirmado',
        'enviado' => 'Enviado',
        'entregado' => 'Entregado',
        'cancelado' => 'Cancelado',
    ],

    'roles' => [
        'admin' => 'Administrador',
        'editor' => 'Editor',
        'lector' => 'Solo lectura',
    ],
];
