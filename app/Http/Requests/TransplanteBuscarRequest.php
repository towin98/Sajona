<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class TransplanteBuscarRequest extends FormRequest
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
            'fecha_inicial'              => 'required|date_format:Y-m-d',
            'fecha_final'                => 'required|date_format:Y-m-d|after_or_equal:fecha_inicial',
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
            'fecha_inicial.required'                  => 'La fecha inicial es requerida.',
            'fecha_final.required'                    => 'La fecha final es requerida.',
            'fecha_inicial.date_format'               => 'El tipo de formato del campo fecha inicial debe ser ej: Y-m-d.',
            'fecha_final.date_format'                 => 'El tipo de formato del campo fecha final debe ser ej: Y-m-d.',
            'fecha_final.after_or_equal'              => 'La fecha fin debe de ser mayor que la fecha inicial.',
        ];
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'message' => 'Error de Validación de Datos',
            'errors'  => $validator->errors()
        ], 422));
    }
}
