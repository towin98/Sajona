<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class cosechaDeleteRequest extends FormRequest
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
            'tp_id'         => 'required|integer'
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
            'tp_id.required'                => 'El Id trasplante a campo es requerido.',
            'tp_id.integer'                 => 'El Id trasplante a campo debe ser númerico.',

        ];
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'message' => 'Error de Validación de Datos',
            'errors'  => $validator->errors()
        ], 422));
    }
}
