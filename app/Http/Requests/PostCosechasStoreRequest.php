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
            'cos_id'                    => 'required|integer|min:0',
            'post_fecha_ini_secado'     => 'required|date_format:Y-m-d',
            'post_fecha_fin_secado'     => 'required|date_format:Y-m-d',
            'post_peso_flor_verde'      => 'required|numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/',
            'post_peso_biomasa'         => 'required|numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/',
            'post_peso_flor_seco'       => 'required|numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/',
            'post_porcentaje_humedad'   => 'required|numeric|regex:/^[\d]{0,3}(\.[\d]{1,2})?$/|max:100',
            'post_cbd'                  => 'required|numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/|max:100',
            'post_thc'                  => 'required|numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/|max:100',
            'post_observacion'          => 'nullable|max:255',
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
            'post_peso_flor_verde.numeric'          => 'El campo peso de flor verde debe ser un dato númerico.',
            'post_peso_flor_verde.regex'            => 'El campo peso de flor verde solo puede tener 11 digitos entreros y 2 decimales máximo.',
            'post_peso_biomasa.required'            => 'El valor de peso biomasa es requerido.',
            'post_peso_biomasa.numeric'             => 'El valor de peso biomasa debe ser un dato númerico.',
            'post_peso_biomasa.regex'               => 'El valor de peso biomasa solo puede tener 11 digitos entreros y 2 decimales máximo.',
            'post_peso_flor_seco.required'          => 'El campo peso flor seco es requerido.',
            'post_peso_flor_seco.numeric'           => 'El campo peso flor seco debe ser un dato númerico.',
            'post_peso_flor_seco.regex'             => 'El campo peso flor seco solo puede tener 11 digitos entreros y 2 decimales máximo.',
            'post_porcentaje_humedad.required'      => 'El campo Porcentaje Humedad es requerido.',
            'post_porcentaje_humedad.numeric'       => 'El campo Porcentaje Humedad debe ser númerico.',
            'post_porcentaje_humedad.regex'         => 'El campo Porcentaje Humedad solo puede tener 2 digitos entreros y 2 decimales máximo.',
            'post_porcentaje_humedad.max'           => 'El campo Porcentaje Humedad no debe ser superior al 100 %.',
            'post_cbd.required'                     => 'El % CBD es requerido.',
            'post_cbd.numeric'                      => 'El % CBD debe ser un dato númerico.',
            'post_cbd.regex'                        => 'El % CBD solo puede tener máximo 11 digitos entreros y 2 decimales.',
            'post_cbd.max'                          => 'El % CBD no debe ser superior al 100 %.',
            'post_thc.required'                     => 'El % THC es requerido.',
            'post_thc.numeric'                      => 'El % THC debe ser un dato númerico.',
            'post_thc.regex'                        => 'El % THC solo puede tener máximo 11 digitos entreros y 2 decimales.',
            'post_thc.max'                          => 'El % THC no debe ser superior al 100 %.',
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
