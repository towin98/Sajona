<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ListarTransplanteBolsaCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'pm_id'                    => $this['get_planta_madre']['pm_id'],
            'id_lote'                  => $this['pro_id_lote'],
            'fecha_propagacion'        => $this['pro_fecha'],
            'pm_fecha_esquejacion'     => $this['get_planta_madre']['pm_fecha_esquejacion'],
            'fecha_transplante'        => $this['get_planta_madre']['get_transplante'] == null ? '' : $this['get_planta_madre']['get_transplante']['tp_fecha'],
            'accion'                   => $this['get_planta_madre']['get_transplante'] == null || $this['get_planta_madre']['get_transplante']['tp_fecha'] == '0000-00-00 00:00:00' ? 'Pendiente' : 'Finalizado'
        ];
    }
}
