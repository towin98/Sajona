<?php

namespace App\Http\Resources;

use App\Traits\alertaTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class listarCosechaCollection extends ResourceCollection
{
    use alertaTrait;

    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        // Se armana data como la requiere, tipo object.
        $data = new Request([
            'pro_fecha' => $this['get_planta_madre']['get_propagacion']['pro_fecha'],
            'getPlantaMadre' => new Request([
                'pm_id' => $this['get_planta_madre']['pm_id']
            ])
        ]);

        $this->fnconsultarRangosAlerta();

        $arrAlerta = $this->alerta($data);

        $evento            = $arrAlerta[0];
        $color             = $arrAlerta[1];
        $diasTranscurridos = $arrAlerta[2];

        $fechaCosecha = $this['get_cosecha'] != null ? $this['get_cosecha']['cos_fecha_cosecha'] : '';
        if ($fechaCosecha == "") {
            $estado = "Pendiente";
        }else{
            $estado = "Finalizado";
        }

        return [
            'id_lote'                       => $this['get_planta_madre']['pm_pro_id_lote'],
            'tp_id'                         => $this['tp_id'],
            'pro_cantidad_plantas_madres'   => $this['get_planta_madre']['get_propagacion']['pro_cantidad_plantas_madres'],
            'tp_fecha'                      => $this['tp_fecha'],
            'cos_fecha_cosecha'             => $this['get_cosecha'] != null ? $this['get_cosecha']['cos_fecha_cosecha'] : '',
            'estado_lote'                   => $evento,
            'dias_transcurridos'            => $diasTranscurridos,
            'color'                         => $color,
            'estado'                        => $estado
        ];
    }
}
