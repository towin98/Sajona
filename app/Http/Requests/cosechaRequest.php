<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class cosechaRequest extends FormRequest
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
     * Reglas de validación.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'cos_fecha_cosecha'          => "required|date_format:Y-m-d|before_or_equal:hoy", // Se envia en el request fecha actual en el campo hoy
            'cos_numero_plantas'         => 'required|integer',
            'cos_estado_cosecha'         => 'required|string',
            'cos_dias_floracion'         => 'required|integer',
            'cos_peso_verde'             => 'required|integer',
            'cos_observacion'            => 'nullable|string|max:255',
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
            'cos_fecha_cosecha.required'             => 'El campo fecha Cosecha es requerido.',
            'cos_fecha_cosecha.date_format'          => 'El tipo de formato del campo fecha Cosecha debe ser ej: Y-m-d.',
            'cos_fecha_cosecha.before_or_equal'       => 'La fecha de cosecha no puede ser mayor a la de hoy.',
            'cos_numero_plantas.required'            => 'El numero de plantas es requerido.',
            'cos_numero_plantas.integer'             => 'El numero de plantas debe ser númerico.',
            'cos_estado_cosecha.required'            => 'El estado de la cosecha es requerido.',
            'cos_estado_cosecha.string'              => 'El estado de la cosecha debe ser un string.',
            'cos_dias_floracion.required'            => 'Los días de floración es requerido.',
            'cos_dias_floracion.integer'             => 'Los días de floración es debe ser númerico.',
            'cos_peso_verde.required'                => 'El peso verde campo es requerido.',
            'cos_peso_verde.integer'                 => 'El peso verde campo debe ser númerico.',
            'cos_observacion.max'                    => 'Supero el número máximo de caracteres permitido 255.',
        ];
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'message' => 'Error de Validación de Datos',
            'errors'  => $validator->errors()
        ], 422));
    }
}
