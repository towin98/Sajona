<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AlertaRequest extends FormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'min_rang_propagacion'  => 'required|integer|min:0',
            'max_rang_propagacion'  => 'required|integer|min:0',
            'min_rang_bolsa'        => 'required|integer|min:0',
            'max_rang_bolsa'        => 'required|integer|min:0',
            'min_rang_campo'        => 'required|integer|min:0',
            'max_rang_campo'        => 'required|integer|min:0',
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
            'min_rang_propagacion.required'  => 'El rango mínimo de Propagación es requerido.',
            'min_rang_propagacion.integer'   => 'El rango mínimo de Propagación debe ser entero.',
            'min_rang_propagacion.min'       => 'El rango mínimo de Propagación debe ser al menos 0.',

            'max_rang_propagacion.required'  => 'El rango máximo de Propagación es requerido.',
            'max_rang_propagacion.integer'   => 'El rango máximo de Propagación debe ser entero.',
            'max_rang_propagacion.min'       => 'El rango maximo de Propagación debe ser al menos 0.',

            'min_rang_bolsa.required'        => 'El rango mínimo de Trans. Bolsa es requerido.',
            'min_rang_bolsa.integer'         => 'El rango mínimo de Trans. Bolsa debe ser entero.',
            'min_rang_bolsa.min'             => 'El rango mínimo de Trans. Bolsa debe ser al menos 0.',

            'max_rang_bolsa.required'        => 'El rango máximo de Trans. Bolsa es requerido.',
            'max_rang_bolsa.integer'         => 'El rango máximo de Trans. Bolsa debe ser entero.',
            'max_rang_bolsa.min'             => 'El rango máximo de Trans. Bolsa debe ser al menos 0.',

            'min_rang_campo.required'        => 'El rango mínimo de Trans. Campo es requerido.',
            'min_rang_campo.integer'         => 'El rango mínimo de Trans. Campo debe ser entero.',
            'min_rang_campo.min'             => 'El rango mínimo de Trans. Campo debe ser al menos 0.',

            'max_rang_campo.required'        => 'El rango máximo de Trans. Campo es requerido.',
            'max_rang_campo.integer'         => 'El rango máximo de Trans. Campo debe ser entero.',
            'max_rang_campo.min'             => 'El rango máximo de Trans. Campo debe ser al menos 0.',
        ];
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'message' => 'Error de Validación de Datos',
            'errors'  => $validator->errors()
        ], 422));
    }
}
