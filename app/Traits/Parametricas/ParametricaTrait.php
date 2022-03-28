<?php

namespace App\Traits\Parametricas;

use App\Models\parametros\TipoPropagacion;

trait ParametricaTrait {

    /**
     * Diccionario de datos para almacenar los Modelos de las paramétricas.
     *
     * @var array
     */
    public $arrayModelos = [
        'pr_tipo_propagacion'                          => TipoPropagacion::class
    ];

    /**
     * Diccionario de datos para almacenar campos parametros.
     *
     * @var array
     */
    public $arrayCamposParametros = [
        // Campos para el módulo de Propagación
        'pr_tipo_propagacion'                          => 'Tipo de propagación'

        // Campos para el módulo de .....
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
