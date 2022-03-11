<?php
namespace App\Traits;

use App\Models\Transplante;

trait alertaTrait {

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
            if ($diasTranscurridos <= 18) {

                $arrAlerta[0] = "Fase inicial.";
                $arrAlerta[1] = "#008F39";

            }else if($diasTranscurridos > 18 && $diasTranscurridos <= 20){
                $arrAlerta[0] = "Cercano transplante a bolsa.";
                $arrAlerta[1] = "#ff8000";
            }else if($diasTranscurridos >= 21){
                $arrAlerta[0] = "Transplantar a Bolsa.";
                $arrAlerta[1] = "#FF0000";
            }
        }else{

            // Buscar transplante campo.
            $transplante = transplante::select(['tp_fecha'])
                ->where('tp_pm_id', optional($data->getPlantaMadre)->pm_id)
                ->where('tp_tipo', 'campo')
                ->get();

            if (count($transplante) == 0) {
                if ($diasTranscurridos < 148) {
                    $arrAlerta[0] = "En Bolsa.";
                    $arrAlerta[1] = "#008F39";
                }else if($diasTranscurridos >= 148 && $diasTranscurridos <= 149){
                    $arrAlerta[0] = "Cercano Transplante a Campo.";
                    $arrAlerta[1] = "#ff8000";
                }else if($diasTranscurridos > 149){
                    $arrAlerta[0] = "Transplantar a Campo.";
                    $arrAlerta[1] = "#FF0000";
                }
            }else{
                $arrAlerta[0] = "En Campo.";
                $arrAlerta[1] = "#008f39";
            }

        }
        $arrAlerta[2] = $diasTranscurridos;
        return $arrAlerta;
    }
}
?>
