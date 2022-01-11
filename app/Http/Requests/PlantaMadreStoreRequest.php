<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PlantaMadreStoreRequest extends FormRequest
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
            'pm_fecha_esquejacion'          => 'required|date_format:Y-m-d',
            'pm_cantidad_esquejes'          => 'required|integer',
            'pm_cantidad_semillas'          => 'required|integer',
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
            'pm_fecha_esquejacion.required'                => 'La fecha de Esquejación es requerida.',
            'pm_fecha_esquejacion.date_format'             => 'La fecha de Esquejación debe cumplir el formato: Y-m-d.',
            'pm_cantidad_esquejes.integer'                 => 'La cantidad de Esquejes debe ser númerico.',
            'pm_cantidad_esquejes.required'                => 'La cantidad de Esquejes es requerida.',
            'pm_cantidad_semillas.integer'                 => 'La cantidad de Semillas debe ser númerico.',
            'pm_cantidad_semillas.required'                => 'La cantidad de semillas es requerida.',
        ];
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'message' => 'Error de Validación de Datos',
            'errors'  => $validator->errors()
        ], 422));
    }
}
