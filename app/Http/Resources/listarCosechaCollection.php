<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class listarCosechaCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id_lote'                       => $this['get_planta_madre']['pm_pro_id_lote'],
            'tp_id'                         => $this['tp_id'],
            'pro_cantidad_plantas_madres'   => $this['get_planta_madre']['get_propagacion']['pro_cantidad_plantas_madres'],
            'tp_fecha'                      => $this['tp_fecha'],
            'cos_fecha_cosecha'             => $this['get_cosecha'] != null ? $this['get_cosecha']['cos_fecha_cosecha'] : '',
            'estado'                        => 'ESTADO'
        ];
    }
}
