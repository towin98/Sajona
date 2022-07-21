<?php

namespace App\Http\Resources;

use App\Traits\alertaTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ListarPostCosechaCollection extends ResourceCollection
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
            // 'pro_fecha' => , // No es necesario enviar, porque se estan mostrando solo los lote de post cosecha.
            'getPlantaMadre' => new Request([
                'pm_id' => $this['get_trasplante_campo']['get_planta_madre']['pm_id']
            ])
        ]);

        $this->fnconsultarRangosAlerta();

        $arrAlerta = $this->alerta($data);

        $evento            = $arrAlerta[0];
        $color             = $arrAlerta[1];
        $diasTranscurridos = $arrAlerta[2];

        $fecha_ini_secado = (!is_null($this['get_post_cosecha']) ? $this['get_post_cosecha']['post_fecha_ini_secado'] : 'Sin Fecha');
        $fecha_fin_secado = (!is_null($this['get_post_cosecha']) ? $this['get_post_cosecha']['post_fecha_fin_secado'] : 'Sin Fecha');

        $estado = ($fecha_ini_secado === "Sin Fecha" && $fecha_fin_secado === "Sin Fecha") ? "Pendiente" : "Finalizado";

        return [
            'id_lote'                       => $this['get_trasplante_campo']['get_planta_madre']['pm_pro_id_lote'],
            'cos_id'                        => $this['cos_id'],
            'pos_id'                        => (!is_null($this['get_post_cosecha']) ? $this['get_post_cosecha']['pos_id'] : ''),
            'post_fecha_ini_secado'         => $fecha_ini_secado,
            'post_fecha_fin_secado'         => $fecha_fin_secado,
            'estado_lote'                   => $evento,
            'dias_transcurridos'            => $diasTranscurridos,
            'color'                         => $color,
            'estado'                        => $estado
        ];
    }
}
