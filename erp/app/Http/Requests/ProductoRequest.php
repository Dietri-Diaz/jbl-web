<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductoRequest extends FormRequest
{
    /** Grados de protección que se pueden elegir en el formulario. */
    public const RESISTENCIAS = ['IPX4', 'IPX5', 'IPX7', 'IP67', 'IP68'];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Al crear, la foto es obligatoria; al editar, solo si se cambia.
        $esNuevo = $this->isMethod('post');

        $reglas = [
            'nombre' => ['required', 'string', 'max:80'],
            'categoria_id' => ['required', 'integer', 'exists:categorias,id'],
            'tipo' => ['required', 'string', 'max:40'],
            'sku' => ['nullable', 'string', 'max:30', Rule::unique('productos', 'sku')->ignore($this->route('producto'))],
            'color' => ['nullable', 'string', 'max:30'],
            'descripcion' => ['nullable', 'string', 'max:2000'],

            'precio' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            // La oferta es opcional, pero si se escribe debe ser más barata que el precio.
            'precio_oferta' => ['nullable', 'numeric', 'min:0.01', 'lt:precio'],

            // La ficha técnica es opcional: unos audífonos no tienen vatios.
            'potencia_w' => ['nullable', 'integer', 'min:1', 'max:2000'],
            'bateria_h' => ['nullable', 'integer', 'min:0', 'max:100'],
            'resistencia' => ['nullable', Rule::in(self::RESISTENCIAS)],

            'foto' => [$esNuevo ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'foto_alt' => ['required', 'string', 'max:160'],

            'visible' => ['nullable', 'boolean'],
            'destacado' => ['nullable', 'boolean'],
            'sello' => ['nullable', 'string', 'max:30'],
            'orden' => ['nullable', 'integer', 'min:0', 'max:999'],

            'stock_minimo' => ['required', 'integer', 'min:0', 'max:9999'],
        ];

        // El stock solo se escribe al crear; después cambia con los ajustes de inventario.
        if ($esNuevo) {
            $reglas['stock_inicial'] = ['nullable', 'integer', 'min:0', 'max:99999'];
        }

        return $reglas;
    }

    public function messages(): array
    {
        return [
            'precio_oferta.lt' => 'El precio de oferta debe ser menor que el precio normal.',
            'foto.required' => 'Sube una foto del producto.',
            'foto.max' => 'La foto no puede pesar más de 4 MB.',
            'foto.mimes' => 'La foto debe ser JPG, PNG o WEBP.',
            'sku.unique' => 'Ya existe otro producto con ese SKU.',
            'categoria_id.required' => 'Elige una categoría.',
        ];
    }
}
