<?php
namespace App\Traits;

use App\Models\Alerta;
use App\Models\Cosecha;
use App\Models\Transplante;

trait alertaTrait {

    private $max_rang_propagacion;
    private $max_rang_bolsa;
    private $max_rang_campo;

    /**
      * Alerta estado de lotes.
      *
      * @param object $data
      * @return array Estado de lote [0], color del estado [1]
      */
    public function alerta($data)
    {
        $arrAlerta = [];

        // Dias transcurridos desde la fecha de propagacion hasta el dia de hoy.
        $today = date_create();
        $diasTranscurridos = date_diff(date_create($data->pro_fecha),$today)->format('%a');

        // Buscar transplante bolsa.
        $transplante = Transplante::select(['tp_fecha'])
            ->where('tp_pm_id', optional($data->getPlantaMadre)->pm_id )
            ->where('tp_tipo', 'bolsa')
            ->get();
        if (count($transplante) == 0) {
            if ($diasTranscurridos <= ($this->max_rang_propagacion-2) ) {// rang max propagacion -2
                $arrAlerta[0] = "Fase inicial";
                $arrAlerta[1] = "#008F39";
            }else if($diasTranscurridos > ($this->max_rang_propagacion-2) && $diasTranscurridos <= ($this->max_rang_propagacion) ){// rang maxpropagacion (menos -2 )
                $arrAlerta[0] = "Cercano transplante a bolsa";
                $arrAlerta[1] = "#ff8000";
            }else if($diasTranscurridos > ($this->max_rang_propagacion) ){ // rang max propagacion
                $arrAlerta[0] = "Transplantar a Bolsa";
                $arrAlerta[1] = "#FF0000";
            }
        }else{
            // Si encuentra transplante a bolsa se busca si tiene transplante a campo.

            // Buscando transplante campo.
            $transplante = transplante::select(['tp_id','tp_fecha'])
                ->where('tp_pm_id', optional($data->getPlantaMadre)->pm_id)
                ->where('tp_tipo', 'campo')
                ->first();

            if (!$transplante) {
                if ($diasTranscurridos < ($this->max_rang_bolsa-1) ) { // rang max trans. bolsa (menos - 1 dia)
                    $arrAlerta[0] = "En Bolsa";
                    $arrAlerta[1] = "#008F39";
                }else if($diasTranscurridos >= ($this->max_rang_bolsa-1) /* rang max trans. bolsa (menos - 1 dia), para armar rango cercano */ && $diasTranscurridos <= ($this->max_rang_bolsa) ){ // rang max trans. bolsa
                    $arrAlerta[0] = "Cercano Transplante a Campo";
                    $arrAlerta[1] = "#ff8000";
                }else if($diasTranscurridos > ($this->max_rang_bolsa) ){ // rang max trans. bolsa
                    $arrAlerta[0] = "Transplantar a Campo";
                    $arrAlerta[1] = "#FF0000";
                }
            }else{
                if ($diasTranscurridos < ($this->max_rang_campo-1) ) { // rang max trans. Campo (menos - 1 dia)
                    $arrAlerta[0] = "En Campo";
                    $arrAlerta[1] = "#008F39";
                }else if($diasTranscurridos >= ($this->max_rang_campo-1) /* rang max trans. Campo (menos - 1 dia), armar rango cercano */  && $diasTranscurridos <= ($this->max_rang_campo)){
                    $arrAlerta[0] = "Casi listo para Cosecha";
                    $arrAlerta[1] = "#ff8000";
                }else if($diasTranscurridos > ($this->max_rang_campo) ){ // rang max trans. Campo

                    // Consultando Cosecha, para saber si ya las plantas estan listas para cosecha.
                    $cosecha = Cosecha::select(['cos_id'])->where('cos_tp_id', $transplante->tp_id)->where('cos_estado',1)->first();
                    if ($cosecha) {
                        // Si existe
                        $arrAlerta[0] = "Cosechado";
                        $arrAlerta[1] = "#008F39";
                    }else{
                        // Si no hay registros en Cosecha.
                        $arrAlerta[0] = "Listo para Cosecha";
                        $arrAlerta[1] = "#FF0000";
                    }

                }
            }

        }
        $arrAlerta[2] = $diasTranscurridos;
        return $arrAlerta;
    }

    public function fnconsultarRangosAlerta()
    {
        $alerta = Alerta::first();

        $this->max_rang_propagacion = $alerta->max_rang_propagacion;
        $this->max_rang_bolsa       = $alerta->max_rang_bolsa;
        $this->max_rang_campo       = $alerta->max_rang_campo;
    }
}
?>
