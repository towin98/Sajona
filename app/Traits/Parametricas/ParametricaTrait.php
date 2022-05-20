<?php

namespace App\Traits\Parametricas;

use App\Models\Parametros\EstadoCosecha;
use App\Models\Parametros\FaseCultivo;
use App\Models\Parametros\MotivoPerdida;
use App\Models\Parametros\TipoIncorporacion;
use App\Models\Parametros\TipoLote;
use App\Models\parametros\TipoPropagacion;
use App\Models\Parametros\Ubicacion;
use App\Models\Parametros\Variedad;

trait ParametricaTrait {

    static $messages = [
        'nombre.required'               => 'El Nombre como parametro es requerido.',
        'nombre.max'                    => 'El Nombre no debe superar los 50 carácteres.',
        // 'descripcion.required'          => 'La Descripción es requerida.',
        'descripcion.max'               => 'La Descripcion no debe superar los 50 carácteres.',
        'estado.required'               => 'El Estado es requerido.',
        'estado.in'                     => 'El Estado debe ser ACTIVO o INACTIVO.',
    ];

    static $rules = [
        'nombre'                  => 'required|string|max:50',
        'descripcion'             => 'nullable|string|max:50',
        'estado'                  => 'required|string|in:ACTIVO,INACTIVO',
    ];

    /**
     * Diccionario de datos para almacenar los Modelos de las paramétricas.
     *
     * @var array
     */
    public $arrayModelos = [

        // Propagacion
        'pr_tipo_propagacion'                    => TipoPropagacion::class,
        'pr_variedad'                            => Variedad::class,
        'pr_tipo_incorporacion'                  => TipoIncorporacion::class,

        // Cosecha
        'pr_estado_cosecha'                      => EstadoCosecha::class,

        // Tras. Bolsa
        'pr_tipo_lote'                           => TipoLote::class,
        'pr_ubicacion'                           => Ubicacion::class,

        // Bajas
        'pr_fase_cultivo'                        => FaseCultivo::class,
        'pr_motivo_perdida'                       => MotivoPerdida::class,
    ];

    /**
     * Diccionario de datos para almacenar campos parametros.
     *
     * @var array
     */
    public $arrayCamposParametros = [

        // Campos para el módulo de Propagación
        'pr_tipo_propagacion'                    => 'Tipo de propagación',
        'pr_variedad'                            => 'Variedad',
        'pr_tipo_incorporacion'                  => 'Tipo de Incorporación',

        // Cosecha
        'pr_estado_cosecha'                      => 'Estado de Cosecha',

        // Tras. Bolsa
        'pr_tipo_lote'                           => 'Tipo de Lote',
        'pr_ubicacion'                           => 'Ubicación',

        // Bajas
        'pr_fase_cultivo'                        => 'Fase del cultivo',
        'pr_motivo_perdida'                       => 'Motivo perdida',
    ];

    /**
     * Obtiene el Modelo de una paramétrica.
     *
     * @param string $parametrica Nombre de la paramétrica
     * @return string namespace del Modelo asociado a la paramétrica.
     */
    public function getModelo($parametrica){
        return isset($this->arrayModelos[$parametrica]) ? $this->arrayModelos[$parametrica] : '';
    }

    /**
     * Obtiene el nombre del campo parametro.
     *
     * @param string $parametrica Nombre de la tabla parametro para el campo.
     * @return string nombre del campo parámetro.
     */
    public function getCampoParametro($parametrica){
        return isset($this->arrayCamposParametros[$parametrica]) ? $this->arrayCamposParametros[$parametrica] : '';
    }
}
?>
