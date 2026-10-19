<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ParlanteRequest extends FormRequest
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

        return [
            'nombre' => ['required', 'string', 'max:80'],
            'tipo' => ['required', 'string', 'max:40'],
            'potencia_w' => ['required', 'integer', 'min:1', 'max:2000'],
            'bateria_h' => ['required', 'integer', 'min:0', 'max:100'],
            'resistencia' => ['required', 'in:'.implode(',', self::RESISTENCIAS)],
            'foto' => [$esNuevo ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'foto_alt' => ['required', 'string', 'max:160'],
            'sello' => ['nullable', 'string', 'max:30'],
            'orden' => ['nullable', 'integer', 'min:0', 'max:999'],
        ];
    }
}
