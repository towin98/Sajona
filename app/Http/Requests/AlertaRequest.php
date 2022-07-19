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
            'max_rang_propagacion'  => 'required|integer|min:0',
            'max_rang_bolsa'        => 'required|integer|min:0',
            'max_rang_campo'        => 'required|integer|min:0',
            'max_rang_cosecha'      => 'required|integer|min:0',
            'max_rang_post_cosecha' => 'required|integer|min:0'
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

            'max_rang_propagacion.required'     => 'El rango máximo en días para Propagación es requerido.',
            'max_rang_propagacion.integer'      => 'El rango máximo en días para Propagación debe ser entero.',
            'max_rang_propagacion.min'          => 'El rango maximo en días para Propagación debe ser al menos 0.',

            'max_rang_bolsa.required'           => 'El rango máximo en días para Tras. Bolsa es requerido.',
            'max_rang_bolsa.integer'            => 'El rango máximo en días para Tras. Bolsa debe ser entero.',
            'max_rang_bolsa.min'                => 'El rango máximo en días para Tras. Bolsa debe ser al menos 0.',

            'max_rang_campo.required'           => 'El rango máximo en días para Tras. Campo es requerido.',
            'max_rang_campo.integer'            => 'El rango máximo en días para Tras. Campo debe ser entero.',
            'max_rang_campo.min'                => 'El rango máximo en días para Tras. Campo debe ser al menos 0.',

            'max_rang_cosecha.required'         => 'El rango máximo en días para cosecha es requerido.',
            'max_rang_cosecha.integer'          => 'El rango máximo en días para cosecha debe ser entero.',
            'max_rang_cosecha.min'              => 'El rango máximo en días para cosecha debe ser al menos 0.',

            'max_rang_post_cosecha.required'    => 'El rango máximo en días para post cosecha es requerido.',
            'max_rang_post_cosecha.integer'     => 'El rango máximo en días para post cosecha debe ser entero.',
            'max_rang_post_cosecha.min'         => 'El rango máximo en días para post cosecha debe ser al menos 0.',
        ];
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'message' => 'Error de Validación de Datos',
            'errors'  => $validator->errors()
        ], 422));
    }
}
