<?php

// Catálogo inicial de la tienda, agrupado por categoría.
//
// Los modelos son reales y cada uno tiene su fotografía con licencia libre en
// assets/img/productos/<slug>.jpg (créditos en docs/creditos-imagenes.md).
// Los PRECIOS y el STOCK son datos de ejemplo para la demostración.
// La ficha técnica solo incluye los datos confirmados de cada modelo: donde
// no hay un dato seguro, el campo se deja vacío.
//
// Campos opcionales: color, oferta, minimo (stock mínimo, por defecto 5),
// potencia (W), bateria (h), resistencia, sello, destacado.

return [

    [
        'nombre' => 'Parlantes portátiles',
        'slug' => 'parlantes-portatiles',
        'descripcion' => 'Bluetooth, batería para todo el día y carcasa resistente al agua.',
        'productos' => [
            [
                'nombre' => 'JBL Go 4', 'slug' => 'go-4', 'sku' => 'JBL-GO4-MO', 'tipo' => 'Ultra compacto', 'color' => 'Morado',
                'descripcion' => 'La generación más reciente del parlante de bolsillo: a prueba de polvo y agua, con correa de tela para colgarlo de donde quieras.',
                'precio' => 199.00, 'stock' => 40, 'destacado' => true,
                'potencia' => 4, 'bateria' => 7, 'resistencia' => 'IP67', 'sello' => 'Nuevo',
                'alt' => 'Parlante compacto JBL Go 4 morado con correa de tela, visto de frente en un exhibidor',
            ],
            [
                'nombre' => 'JBL Go 2', 'slug' => 'go-2', 'sku' => 'JBL-GO2-AZ', 'tipo' => 'Ultra compacto', 'color' => 'Azul',
                'descripcion' => 'Cabe en la palma de la mano y pesa menos de 200 gramos. Sumergible, con 5 horas de música y micrófono para atender llamadas.',
                'precio' => 129.00, 'oferta' => 99.00, 'stock' => 34,
                'potencia' => 3, 'bateria' => 5, 'resistencia' => 'IPX7',
                'alt' => 'Parlante JBL Go 2 azul sostenido en la palma de una mano',
            ],
            [
                'nombre' => 'JBL Go', 'slug' => 'go', 'sku' => 'JBL-GO1-RJ', 'tipo' => 'Ultra compacto', 'color' => 'Rojo',
                'descripcion' => 'El primer Go: un cubo de 3 vatios con entrada auxiliar y manos libres. Para tener música en el escritorio o en la mochila.',
                'precio' => 89.00, 'stock' => 3,
                'potencia' => 3, 'bateria' => 5,
                'alt' => 'Parlante compacto JBL Go rojo de primera generación, de pie sobre una superficie de madera',
            ],
            [
                'nombre' => 'JBL Clip 5', 'slug' => 'clip-5', 'sku' => 'JBL-CLP5-AR', 'tipo' => 'Con mosquetón', 'color' => 'Arena',
                'descripcion' => 'Se engancha a la mochila, al cinturón o a la bicicleta con su mosquetón integrado. A prueba de polvo y agua.',
                'precio' => 329.00, 'stock' => 16,
                'potencia' => 7, 'bateria' => 12, 'resistencia' => 'IP67',
                'alt' => 'Parlante JBL Clip 5 color arena con mosquetón integrado y logotipo grande, en un exhibidor',
            ],
            [
                'nombre' => 'JBL Clip 2', 'slug' => 'clip-2', 'sku' => 'JBL-CLP2-AZ', 'tipo' => 'Con mosquetón', 'color' => 'Azul',
                'descripcion' => 'Redondo, sumergible y con mosquetón. Ocho horas de música para llevar colgadas a cualquier parte.',
                'precio' => 219.00, 'oferta' => 179.00, 'stock' => 11,
                'potencia' => 3, 'bateria' => 8, 'resistencia' => 'IPX7',
                'alt' => 'Parlante redondo JBL Clip 2 azul con su mosquetón naranja, sobre un fondo de colores',
            ],
            [
                'nombre' => 'JBL Flip 5', 'slug' => 'flip-5', 'sku' => 'JBL-FLIP5-GR', 'tipo' => 'Portátil', 'color' => 'Gris',
                'descripcion' => 'El tamaño más pedido de la línea: 20 vatios, 12 horas de batería y carga por USB-C. Sumergible.',
                'precio' => 499.00, 'oferta' => 429.00, 'stock' => 25, 'destacado' => true,
                'potencia' => 20, 'bateria' => 12, 'resistencia' => 'IPX7',
                'alt' => 'Parlante JBL Flip 5 gris sobre un escritorio de madera clara, conectado a su cable de carga',
            ],
            [
                'nombre' => 'JBL Flip 4', 'slug' => 'flip-4', 'sku' => 'JBL-FLIP4-BG', 'tipo' => 'Portátil', 'color' => 'Beige',
                'descripcion' => 'Doce horas de música y cuerpo sumergible, con manos libres para atender llamadas sin sacar el celular.',
                'precio' => 399.00, 'stock' => 14,
                'potencia' => 16, 'bateria' => 12, 'resistencia' => 'IPX7',
                'alt' => 'Parlante cilíndrico JBL Flip 4 de tela beige, acostado sobre una mesa de madera, con el logotipo rojo al centro',
            ],
            [
                'nombre' => 'JBL Flip 3', 'slug' => 'flip-3', 'sku' => 'JBL-FLIP3-TQ', 'tipo' => 'Portátil', 'color' => 'Turquesa',
                'descripcion' => 'El cilindro que hizo famosa a la línea: dos altavoces, radiadores pasivos a los lados y 10 horas de batería.',
                'precio' => 349.00, 'oferta' => 279.00, 'stock' => 18,
                'potencia' => 16, 'bateria' => 10, 'resistencia' => 'IPX5',
                'alt' => 'Parlante JBL Flip 3 turquesa apoyado sobre una mesa de madera',
            ],
            [
                'nombre' => 'JBL Charge 4', 'slug' => 'charge-4', 'sku' => 'JBL-CHG4-NG', 'tipo' => 'Portátil · Powerbank', 'color' => 'Negro',
                'descripcion' => 'Veinte horas de música y un puerto USB que carga tu celular mientras suena. Sumergible hasta un metro.',
                'precio' => 599.00, 'oferta' => 499.00, 'stock' => 22, 'destacado' => true,
                'potencia' => 30, 'bateria' => 20, 'resistencia' => 'IPX7',
                'alt' => 'Parlante JBL Charge 4 negro sobre una superficie naranja',
            ],
            [
                'nombre' => 'JBL Charge 3', 'slug' => 'charge-3', 'sku' => 'JBL-CHG3-NG', 'tipo' => 'Portátil · Powerbank', 'color' => 'Negro',
                'descripcion' => 'Batería de 20 horas que también carga tu celular por USB. Cuerpo sumergible y graves con radiadores pasivos.',
                'precio' => 499.00, 'stock' => 8,
                'potencia' => 20, 'bateria' => 20, 'resistencia' => 'IPX7',
                'alt' => 'Parlante JBL Charge 3 negro visto de frente sobre un mantel estampado, con el logotipo naranja y los botones superiores',
            ],
            [
                'nombre' => 'JBL Pulse 5', 'slug' => 'pulse-5', 'sku' => 'JBL-PLS5-NG', 'tipo' => 'Portátil con luces', 'color' => 'Negro',
                'descripcion' => 'Todo el cuerpo se ilumina al ritmo de la música. Sonido en 360 grados, a prueba de polvo y agua.',
                'precio' => 1099.00, 'stock' => 7,
                'potencia' => 40, 'bateria' => 12, 'resistencia' => 'IP67',
                'alt' => 'Parlante JBL Pulse 5 de cuerpo translúcido y parte superior negra, de pie sobre una base iluminada',
            ],
            [
                'nombre' => 'JBL Xtreme', 'slug' => 'xtreme', 'sku' => 'JBL-XTR-NG', 'tipo' => 'Portátil con correa', 'color' => 'Negro',
                'descripcion' => 'Cuatro altavoces y dos radiadores de graves en un cuerpo que se cuelga del hombro con su correa.',
                'precio' => 899.00, 'stock' => 9,
                'potencia' => 40, 'bateria' => 15, 'resistencia' => 'IPX5',
                'alt' => 'Detalle del radiador pasivo lateral de un parlante JBL Xtreme',
            ],
            [
                'nombre' => 'JBL Tuner FM', 'slug' => 'tuner-fm', 'sku' => 'JBL-TNR-NG', 'tipo' => 'Portátil con radio', 'color' => 'Negro',
                'descripcion' => 'Parlante Bluetooth con radio FM, pantalla y botones para guardar tus emisoras favoritas.',
                'precio' => 349.00, 'stock' => 0,
                'bateria' => 8,
                'alt' => 'Radio parlante portátil JBL Tuner FM negro con pantalla y antena extendida, sobre un fondo rojo',
            ],
        ],
    ],

    [
        'nombre' => 'Parlantes de fiesta',
        'slug' => 'parlantes-de-fiesta',
        'descripcion' => 'Potencia para llenar una azotea, con luces y asa para llevarlos a donde sea.',
        'productos' => [
            [
                'nombre' => 'JBL Boombox 3', 'slug' => 'boombox-3', 'sku' => 'JBL-BB3-NG', 'tipo' => 'Fiesta', 'color' => 'Negro',
                'descripcion' => 'El más potente de la línea portátil: 180 vatios con subwoofer dedicado, a prueba de polvo y agua.',
                'precio' => 2299.00, 'oferta' => 1999.00, 'stock' => 4, 'minimo' => 3, 'destacado' => true,
                'potencia' => 180, 'bateria' => 24, 'resistencia' => 'IP67', 'sello' => 'El más potente',
                'alt' => 'Parlante JBL Boombox 3 negro sujeto por su asa',
            ],
            [
                'nombre' => 'JBL Boombox 2', 'slug' => 'boombox-2', 'sku' => 'JBL-BB2-CM', 'tipo' => 'Fiesta', 'color' => 'Camuflaje',
                'descripcion' => 'Graves profundos, 24 horas de batería y un asa pensada para cargarlo con una mano. Sumergible.',
                'precio' => 1899.00, 'stock' => 6, 'minimo' => 3,
                'potencia' => 80, 'bateria' => 24, 'resistencia' => 'IPX7',
                'alt' => 'Parlante JBL Boombox 2 con acabado de camuflaje y asa de transporte',
            ],
            [
                'nombre' => 'JBL PartyBox On-The-Go', 'slug' => 'partybox-on-the-go', 'sku' => 'JBL-PBOTG-NG', 'tipo' => 'Fiesta con micrófono', 'color' => 'Negro',
                'descripcion' => 'Cien vatios, aro de luces y micrófono inalámbrico incluido para el karaoke. Se lleva al hombro con su correa.',
                'precio' => 1599.00, 'oferta' => 1399.00, 'stock' => 5, 'minimo' => 3, 'destacado' => true,
                'potencia' => 100, 'bateria' => 6, 'resistencia' => 'IPX4',
                'alt' => 'Parlante de fiesta JBL PartyBox On-The-Go negro con aro de luz verde encendido y panel de controles superior',
            ],
            [
                'nombre' => 'JBL PartyBox 710', 'slug' => 'partybox-710', 'sku' => 'JBL-PB710-NG', 'tipo' => 'Fiesta · Con ruedas', 'color' => 'Negro',
                'descripcion' => 'Ochocientos vatios, dos aros de luces y ruedas para moverlo. Funciona enchufado: no tiene batería.',
                'precio' => 3999.00, 'stock' => 2, 'minimo' => 2,
                'potencia' => 800, 'resistencia' => 'IPX4',
                'alt' => 'Parlante de fiesta JBL PartyBox 710 negro, de pie, con dos aros de luz naranja encendidos',
            ],
        ],
    ],

    [
        'nombre' => 'Audífonos',
        'slug' => 'audifonos',
        'descripcion' => 'De diadema o deportivos, con Bluetooth y batería para varios días.',
        'productos' => [
            [
                'nombre' => 'JBL Live 650BTNC', 'slug' => 'live-650btnc', 'sku' => 'JBL-L650-NG', 'tipo' => 'Diadema · Cancelación de ruido', 'color' => 'Negro',
                'descripcion' => 'Audífonos de diadema con cancelación activa de ruido y asistente de voz. Se pliegan para guardarlos.',
                'precio' => 749.00, 'oferta' => 649.00, 'stock' => 12, 'destacado' => true,
                'bateria' => 20,
                'alt' => 'Audífonos de diadema JBL Live 650BTNC negros apoyados sobre una mesa de madera clara',
            ],
            [
                'nombre' => 'JBL Endurance Peak 3', 'slug' => 'endurance-peak-3', 'sku' => 'JBL-EP3-CR', 'tipo' => 'Deportivos inalámbricos', 'color' => 'Coral',
                'descripcion' => 'Audífonos deportivos con gancho para la oreja: no se caen al correr. Resisten el sudor, el polvo y el agua.',
                'precio' => 449.00, 'stock' => 15,
                'bateria' => 50, 'resistencia' => 'IP68',
                'alt' => 'Audífono deportivo JBL Endurance Peak 3 color coral con gancho para la oreja, sostenido en una mano',
            ],
            [
                'nombre' => 'JBL Synchros E50BT', 'slug' => 'synchros-e50bt', 'sku' => 'JBL-E50-NG', 'tipo' => 'Diadema Bluetooth', 'color' => 'Negro y gris',
                'descripcion' => 'Diadema Bluetooth de almohadillas grandes, para escuchar durante horas. También funciona con cable.',
                'precio' => 399.00, 'oferta' => 299.00, 'stock' => 5,
                'bateria' => 18,
                'alt' => 'Audífonos de diadema JBL Synchros E50BT negros con placa plateada, apoyados en un escritorio mientras cargan',
            ],
        ],
    ],

    [
        'nombre' => 'Audio para casa',
        'slug' => 'audio-para-casa',
        'descripcion' => 'Parlantes de sala para escuchar sentado, con el sonido clásico de la marca.',
        'productos' => [
            [
                'nombre' => 'JBL L100 Classic', 'slug' => 'l100-classic', 'sku' => 'JBL-L100-MD', 'tipo' => 'Parlante de sala', 'color' => 'Madera y negro',
                'descripcion' => 'La reedición del parlante más vendido en la historia de JBL: caja de madera, woofer de 12 pulgadas y tres vías. Necesita amplificador.',
                'precio' => 8999.00, 'stock' => 2, 'minimo' => 1,
                'sello' => 'Clásico',
                'alt' => 'Parlante de piso JBL L100 Classic con caja de madera, frente negro y woofer de cono blanco, en una exhibición',
            ],
            [
                'nombre' => 'JBL TI 5000', 'slug' => 'ti-5000', 'sku' => 'JBL-TI5K-NG', 'tipo' => 'Columna de alta fidelidad', 'color' => 'Negro',
                'descripcion' => 'Columna de alta fidelidad de colección, con cuatro altavoces. Pieza para quien busca el sonido JBL de otra época. Necesita amplificador.',
                'precio' => 6499.00, 'stock' => 1, 'minimo' => 1,
                'alt' => 'Columna de alta fidelidad JBL TI 5000 negra con cuatro altavoces y lateral inclinado, en la esquina de una sala',
            ],
            [
                'nombre' => 'JBL Radial', 'slug' => 'radial', 'sku' => 'JBL-RDL-NG', 'tipo' => 'Base parlante', 'color' => 'Negro',
                'descripcion' => 'Base parlante circular con conector para iPod y entrada auxiliar. Una pieza de diseño para el velador o la sala.',
                'precio' => 599.00, 'oferta' => 449.00, 'stock' => 3, 'minimo' => 2,
                'alt' => 'Base parlante circular JBL Radial negra con un iPod Classic acoplado al centro, sobre una mesa de madera',
            ],
        ],
    ],

    [
        'nombre' => 'Audio profesional',
        'slug' => 'audio-profesional',
        'descripcion' => 'Monitores de estudio y sistemas para escenario.',
        'productos' => [
            [
                'nombre' => 'JBL LSR305', 'slug' => 'lsr305', 'sku' => 'JBL-LSR305-PAR', 'tipo' => 'Monitores de estudio · Par', 'color' => 'Negro',
                'descripcion' => 'Monitores activos de 5 pulgadas para producir y mezclar en casa. Se venden por par.',
                'precio' => 1499.00, 'stock' => 6, 'minimo' => 2,
                'potencia' => 82,
                'alt' => 'Par de monitores de estudio JBL LSR305 negros sobre una mesa blanca',
            ],
            [
                'nombre' => 'JBL VRX932LA', 'slug' => 'vrx932la', 'sku' => 'JBL-VRX932', 'tipo' => 'Arreglo lineal', 'color' => 'Negro',
                'descripcion' => 'Módulo de arreglo lineal de 12 pulgadas para eventos y locales medianos. Se monta en soporte o se cuelga en columna.',
                'precio' => 7999.00, 'stock' => 2, 'minimo' => 1,
                'potencia' => 800,
                'alt' => 'Parlante de arreglo lineal JBL VRX932LA de 12 pulgadas montado sobre un soporte',
            ],
            [
                'nombre' => 'JBL VT4886', 'slug' => 'vt4886', 'sku' => 'JBL-VT4886', 'tipo' => 'Arreglo lineal de escenario', 'color' => 'Negro',
                'descripcion' => 'Módulo compacto de la serie VerTec para sistemas de escenario. Se vende por unidad, a pedido.',
                'precio' => 12999.00, 'stock' => 0, 'minimo' => 1,
                'alt' => 'Módulo de arreglo lineal JBL VT4886 apoyado sobre un escenario al aire libre',
            ],
        ],
    ],

];
