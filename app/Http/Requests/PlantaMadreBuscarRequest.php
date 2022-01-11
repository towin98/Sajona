<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PlantaMadreBuscarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Reglas de validación al buscar Planta Madre Buscar lotes.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'fecha_inicio'              => 'required|date_format:Y-m-d',
            'fecha_fin'                 => 'required|date_format:Y-m-d|after_or_equal:fecha_inicio',
        ];
    }

    /**
     * Reglas de validación para el recurso crear.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'fecha_inicio.required'                  => 'La fecha de inicio es requerida.',
            'fecha_fin.required'                     => 'La fecha de fin es requerida.',
            'fecha_inicio.date_format'               => 'El tipo de formato del campo fecha de inicio debe ser ej: Y-m-d.',
            'fecha_fin.date_format'                  => 'El tipo de formato del campo fecha de fin debe ser ej: Y-m-d.',
            'fecha_fin.after_or_equal'               => 'La fecha fin debe de ser mayor que la fecha inicio.',
        ];
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'message' => 'Error de Validación de Datos',
            'errors'  => $validator->errors()
        ], 422));
    }
}
