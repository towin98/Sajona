<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PropationRequest extends FormRequest
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
            'pro_fecha'                     => 'required|date',
            'pro_tipo_propagacion'          => 'required|string',
            'pro_variedad'                  => 'required|integer',
            'pro_tipo_incorporacion'        => 'required|string',
            'pro_cantidad_material'         => 'required|integer',
            'pro_cantidad_plantas_madres'   => 'required|integer',
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
            'pro_fecha.required'                            => 'El campo fecha de propagación es requerido.',
            'pro_fecha.date'                                => 'El tipo de formato del campo fecha de propagación debe ser ej: Y-m-d.',
            'pro_tipo_propagacion.required'                 => 'El campo tipo de propagacion es requerido.',
            'pro_tipo_propagacion.string'                   => 'El campo tipo de propagacion debe ser un string.',
            'pro_variedad.required'                         => 'El campo variedad es requerido.',
            'pro_variedad.integer'                          => 'El campo variedad debe ser númerico.',
            'pro_tipo_incorporacion.required'               => 'El campo tipo incorporacion es requerido.',
            'pro_tipo_incorporacion.string'                 => 'El campo tipo incorporacion debe ser un string.',
            'pro_cantidad_material.required'                => 'El campo cantidad material es requerido.',
            'pro_cantidad_material.integer'                 => 'El campo cantidad material debe ser númerico.',
            'pro_cantidad_plantas_madres.required'          => 'El campo cantidad de plantas madres es requerido.',
            'pro_cantidad_plantas_madres.integer'           => 'El campo cantidad de plantas madres debe ser númerico.',
        ];
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'message' => 'Error de Validación de Datos',
            'errors'  => $validator->errors()
        ], 422));
    }

}


