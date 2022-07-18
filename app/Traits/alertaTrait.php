<?php
namespace App\Traits;

use App\Models\Alerta;
use App\Models\Cosecha;
use App\Models\Trasplante;

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

        // Buscar trasplante bolsa.
        $trasplanteBolsa = Trasplante::select(['tp_fecha'])
            ->where('tp_pm_id', optional($data->getPlantaMadre)->pm_id )
            ->where('tp_tipo', 'bolsa')
            ->get();
        if (count($trasplanteBolsa) == 0) {
            $diasTranscurridos = date_diff(date_create($data->pro_fecha),$today)->format('%a');

            if ($diasTranscurridos <= ($this->max_rang_propagacion-2) ) {// rang max propagacion -2
                $arrAlerta[0] = "Fase inicial";
                $arrAlerta[1] = "#008F39";
            }else if($diasTranscurridos > ($this->max_rang_propagacion-2) && $diasTranscurridos <= ($this->max_rang_propagacion) ){// rang maxpropagacion (menos -2 )
                $arrAlerta[0] = "Cercano trasplante a bolsa";
                $arrAlerta[1] = "#ff8000";
            }else if($diasTranscurridos > ($this->max_rang_propagacion) ){ // rang max propagacion
                $arrAlerta[0] = "Trasplantar a Bolsa";
                $arrAlerta[1] = "#FF0000";
            }
            $arrAlerta[2] = $diasTranscurridos;
        }else{
            // Si encuentra trasplante a bolsa se busca si tiene trasplante a campo.

            // Buscando trasplante campo.
            $trasplanteCampo = trasplante::select(['tp_id','tp_fecha'])
                ->where('tp_pm_id', optional($data->getPlantaMadre)->pm_id)
                ->where('tp_tipo', 'campo')
                ->first();

            if (!$trasplanteCampo) {
                $diasTranscurridos = date_diff(date_create($trasplanteBolsa[0]->tp_fecha),$today)->format('%a');
                if ($diasTranscurridos < ($this->max_rang_bolsa-1) ) { // rang max tras. bolsa (menos - 1 dia)
                    $arrAlerta[0] = "En Bolsa";
                    $arrAlerta[1] = "#008F39";
                }else if($diasTranscurridos >= ($this->max_rang_bolsa-1) /* rang max tras. bolsa (menos - 1 dia), para armar rango cercano */ && $diasTranscurridos <= ($this->max_rang_bolsa) ){ // rang max tras. bolsa
                    $arrAlerta[0] = "Cercano Trasplante a Campo";
                    $arrAlerta[1] = "#ff8000";
                }else if($diasTranscurridos > ($this->max_rang_bolsa) ){ // rang max tras. bolsa
                    $arrAlerta[0] = "Trasplantar a Campo";
                    $arrAlerta[1] = "#FF0000";
                }
                $arrAlerta[2] = $diasTranscurridos;
            }else{

                // Consultando Cosecha, para saber si ya las plantas estan listas para cosecha.
                $cosecha = Cosecha::select([
                        'cos_id',
                        'cos_fecha_cosecha'
                    ])
                    ->where('cos_tp_id', $trasplanteCampo->tp_id)
                    ->where('cos_estado',1)
                    ->first();

                if (!$cosecha) {
                    // Validaciones para trasplante a campo.
                    $diasTranscurridos = date_diff(date_create($trasplanteCampo->tp_fecha),$today)->format('%a');
                    if ($diasTranscurridos < ($this->max_rang_campo-1)) { // rang max tras. Campo (menos - 1 dia)

                        $arrAlerta[0] = "En Campo";
                        $arrAlerta[1] = "#008F39";

                    }else if($diasTranscurridos >= ($this->max_rang_campo-1) /* rang max tras. Campo (menos - 1 dia), armar rango cercano */  &&
                        $diasTranscurridos <= ($this->max_rang_campo)){

                        $arrAlerta[0] = "Casi listo para Cosecha";
                        $arrAlerta[1] = "#ff8000";

                    }else if($diasTranscurridos > ($this->max_rang_campo)){ // rang max tras. Campo
                        // Si no hay registros en Cosecha.
                        $arrAlerta[0] = "Listo para Cosecha";
                        $arrAlerta[1] = "#FF0000";
                    }
                    $arrAlerta[2] = $diasTranscurridos;
                }else{
                    // Validaciones para Alertas Cosecha.
                    $diasTranscurridos = date_diff(date_create($cosecha->cos_fecha_cosecha),$today)->format('%a');
                    $arrAlerta[0] = "Cosechado";
                    $arrAlerta[1] = "#008F39";
                    $arrAlerta[2] = $diasTranscurridos;
                }
            }

        }
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
