<?php

// Mensajes de validación en español (solo las reglas que usa el ERP).
return [
    'boolean' => 'El campo :attribute debe ser verdadero o falso.',
    'confirmed' => 'La confirmación de :attribute no coincide.',
    'email' => 'El campo :attribute debe ser un correo válido.',
    'image' => 'El campo :attribute debe ser una imagen.',
    'in' => 'El valor de :attribute no es válido.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'max' => [
        'numeric' => 'El campo :attribute no puede ser mayor que :max.',
        'file' => 'El campo :attribute no puede pesar más de :max kilobytes.',
        'string' => 'El campo :attribute no puede tener más de :max caracteres.',
    ],
    'mimes' => 'El campo :attribute debe ser un archivo de tipo: :values.',
    'min' => [
        'numeric' => 'El campo :attribute debe ser al menos :min.',
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'required' => 'El campo :attribute es obligatorio.',
    'string' => 'El campo :attribute debe ser un texto.',
    'unique' => 'Ya existe un registro con ese :attribute.',

    'attributes' => [
        'name' => 'nombre',
        'nombre' => 'nombre',
        'email' => 'correo',
        'correo' => 'correo',
        'password' => 'contraseña',
        'mensaje' => 'mensaje',
        'tipo' => 'tipo',
        'potencia_w' => 'potencia',
        'bateria_h' => 'batería',
        'resistencia' => 'resistencia',
        'foto' => 'foto',
        'foto_alt' => 'texto alternativo',
        'sello' => 'sello',
        'orden' => 'orden',
    ],
];
