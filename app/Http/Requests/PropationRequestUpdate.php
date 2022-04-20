<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PropationRequestUpdate extends FormRequest
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
            'pro_fecha'                     => 'nullable|filled|date_format:Y-m-d',
            'pro_tipo_propagacion'          => 'nullable|filled|integer',
            'pro_variedad'                  => 'nullable|filled|integer',
            'pro_tipo_incorporacion'        => 'nullable|filled|integer',
            'pro_cantidad_material'         => 'nullable|filled|integer',
            'pro_cantidad_plantas_madres'   => 'nullable|filled|integer',
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
            'pro_fecha.filled'                              => 'El campo fecha de propagación no puede ser vacía.',
            'pro_fecha.date_format'                         => 'El tipo de formato del campo fecha de propagación debe ser ej: Y-m-d.',
            'pro_tipo_propagacion.filled'                   => 'El campo tipo de propagacion no puede ser vacío.',
            'pro_variedad.filled'                           => 'El campo variedad no puede ser vacía.',
            'pro_variedad.integer'                          => 'El campo variedad debe ser númerico.',
            'pro_tipo_incorporacion.filled'                 => 'El campo tipo incorporacion no puede ser vacío.',
            'pro_cantidad_material.filled'                  => 'El campo cantidad material no puede ser vacío.',
            'pro_cantidad_material.integer'                 => 'El campo cantidad material debe ser númerico.',
            'pro_cantidad_plantas_madres.filled'            => 'El campo cantidad de plantas madres no puede ser vacía.',
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


