<?php
namespace App\Traits;

use App\Models\Transplante;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

trait alertaTrait {

    /**
     * Crea data paginada
     *
     * @param Request $request
     * @param array $registros
     * @param integer $length
     * @return void
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
                $arrAlerta[0] = "fase inicial";
                $arrAlerta[1] = "#ff8000";
            }else if($diasTranscurridos > 18 && $diasTranscurridos < 21 )
            {
                $arrAlerta[0] = "proximo a Bolsa";
                $arrAlerta[1] = "#ff8000";
            }
            else{
                $arrAlerta[0] = "Requiere Transplante a Bolsa.";
                $arrAlerta[1] = "#FF0000";
            }
        }else{

            // Buscar transplante campo.
            $transplante = transplante::select(['tp_fecha'])
                ->where('tp_pm_id', optional($data->getPlantaMadre)->pm_id)
                ->where('tp_tipo', 'campo')
                ->get();
            if (count($transplante) == 0) {
                if ($diasTranscurridos <= 148) {
                    $arrAlerta[0] = "Aun No Requiere Transplante a Campo.";
                    $arrAlerta[1] = "#ff8000";
                }else if($diasTranscurridos > 148 && $diasTranscurridos < 150 )
                {
                    $arrAlerta[0] = "Proximo a transpasar a Campo";
                    $arrAlerta[1] = "#ff8000";
                }
                
                else{
                    $arrAlerta[0] = "Requiere Transplante a Campo.";
                    $arrAlerta[1] = "#FF0000";
                }
            }else{

                $arrAlerta[0] = "Procesos del lote completados.";
                $arrAlerta[1] = "#008f39";
            }

        }
        $arrAlerta[2] = $diasTranscurridos;
        return $arrAlerta;
    }
}
?>
