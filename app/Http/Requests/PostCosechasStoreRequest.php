<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PostCosechasStoreRequest extends FormRequest
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
            'cos_id'                => 'required|integer|min:0',
            'post_fecha_ini_secado' => 'required|date_format:Y-m-d',
            'post_fecha_fin_secado' => 'required|date_format:Y-m-d',
            'post_peso_flor_verde'  => 'required|integer|min:0',
            'post_peso_biomasa'     => 'required|integer|min:0',
            'post_peso_flor_seco'   => 'required|integer|min:0',
            'post_cbd'              => 'required|integer|min:0',
            'post_thc'              => 'required|integer|min:0',
            'post_observacion'      => 'nullable|max:255',
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

            'cos_id.required'                       => 'El id de la cosecha es requerido.',
            'cos_id.integer'                        => 'El id de la cosecha debe ser un dato númerico.',
            'cos_id.min'                            => 'El id de la cosecha debe ser un positivo.',
            'post_fecha_ini_secado.required'        => 'La fecha de inicio secado es requerida.',
            'post_fecha_ini_secado.date_format'     => 'La fecha de inicio secado debe cumplir el formato: Y-m-d.',
            'post_fecha_fin_secado.required'        => 'La fecha de fin secado es requerida.',
            'post_fecha_fin_secado.date_format'     => 'La fecha de fin secado debe cumplir el formato: Y-m-d.',
            'post_peso_flor_verde.required'         => 'El campo peso de flor verde es requerido.',
            'post_peso_flor_verde.integer'          => 'El campo peso de flor verde debe ser un dato númerico.',
            'post_peso_flor_verde.min'              => 'El campo peso de flor verde debe ser positivo.',
            'post_peso_biomasa.required'            => 'El valor de peso biomasa es requerido.',
            'post_peso_biomasa.integer'             => 'El valor de peso biomasa debe ser un dato númerico.',
            'post_peso_biomasa.min'                 => 'El valor de peso biomasa debe ser positivo.',
            'post_peso_flor_seco.required'          => 'El campo peso flor seco es requerido.',
            'post_peso_flor_seco.integer'           => 'El campo peso flor seco debe ser un dato númerico.',
            'post_peso_flor_seco.min'               => 'El campo peso flor seco debe ser positivo.',
            'post_cbd.required'                     => 'El % CBD es requerido.',
            'post_cbd.integer'                      => 'El % CBD debe ser un dato númerico.',
            'post_cbd.min'                          => 'El % CBD debe ser positivo.',
            'post_thc.required'                     => 'El % THC es requerido.',
            'post_thc.integer'                      => 'El % THC debe ser un dato númerico.',
            'post_thc.min'                          => 'El % THC debe ser positivo.',
            'post_observacion.required'             => 'La observación es requerido.',
            'post_observacion.max'                  => 'La observación no puede superar los 255 carácteres.',

        ];
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'message' => 'Error de Validación de Datos',
            'errors'  => $validator->errors()
        ], 422));
    }
}
