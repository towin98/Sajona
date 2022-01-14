<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class TransplanteStoreRequest extends FormRequest
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
            'tp_pm_id'                      => 'required|integer',
            'tp_tipo'                       => 'required|string|max:20',
            'tp_tipo_lote'                  => 'required|string|max:20',
            'tp_fecha'                      => 'required|date_format:Y-m-d',
            'tp_ubicacion'                  => 'required|string|max:20',
            'tp_cantidad_area'              => 'required|integer',
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
            'tp_pm_id.required'                      => 'El id del lote es requerido.',
            'tp_pm_id.integer'                       => 'El id del lote debe ser un dato númerico.',
            'tp_tipo.required'                       => 'El tipo de transplante es requerido.',
            'tp_tipo.string'                         => 'El tipo de transplante debe ser un string.',
            'tp_tipo.max'                            => 'El tipo de transplante no debe superar ls 20 carácteres.',
            'tp_tipo_lote.required'                  => 'El tipo de lote es requerido.',
            'tp_tipo_lote.string'                    => 'El tipo de lote debe ser un string.',
            'tp_tipo_lote.max'                       => 'El tipo de lote no debe superar los 20 carácteres.',
            'tp_fecha.required'                      => 'La fecha de transplante es requerida.',
            'tp_fecha.date_format'                   => 'La fecha de Transplante debe cumplir el formato: Y-m-d.',
            'tp_ubicacion.required'                  => 'La ubicación es requerida.',
            'tp_ubicacion.string'                    => 'La ubicación debe ser un string',
            'tp_ubicacion.max'                       => 'La ubicación no debe superar los 20 carácteres.',
            'tp_cantidad_area.required'              => 'La cantidad área es requerida.',
            'tp_cantidad_area.integer'               => 'La cantidad área debe ser un dato númerico.'
        ];
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'message' => 'Error de Validación de Datos',
            'errors'  => $validator->errors()
        ], 422));
    }
}
