<?php

namespace App\Http\Resources;

use App\Traits\alertaTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ListarTransplanteBolsaCollection extends ResourceCollection
{
    use alertaTrait;
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // Se armana data como la requiere, tipo object.
        $data = new Request([
                'pro_fecha' => $this['pro_fecha'],
                'getPlantaMadre' => new Request([
                    'pm_id' => $this['get_planta_madre']['pm_id']
                ])
        ]);

        $arrAlerta = $this->alerta($data);

        $evento            = $arrAlerta[0];
        $color             = $arrAlerta[1];
        $diasTranscurridos = $arrAlerta[2];

        return [
            'pm_id'                    => $this['get_planta_madre']['pm_id'],
            'id_lote'                  => $this['pro_id_lote'],
            'fecha_propagacion'        => $this['pro_fecha'],
            'pm_fecha_esquejacion'     => $this['get_planta_madre']['pm_fecha_esquejacion'],
            'fecha_transplante'        => $this['get_planta_madre']['get_transplante'] == null ? '' : $this['get_planta_madre']['get_transplante']['tp_fecha'],
            'accion'                   => $this['get_planta_madre']['get_transplante'] == null || $this['get_planta_madre']['get_transplante']['tp_fecha'] == '0000-00-00 00:00:00' ? 'Pendiente' : 'Finalizado',
            'estado_lote'              => $evento,
            'dias_transcurridos'       => $diasTranscurridos,
            'color'                    => $color
        ];
    }
}
