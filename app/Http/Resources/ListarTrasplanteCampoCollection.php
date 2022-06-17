<?php

namespace App\Http\Resources;

use App\Models\Baja;
use App\Traits\alertaTrait;
use App\Traits\bajasTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ListarTrasplanteCampoCollection extends ResourceCollection
{
    use alertaTrait;
    use bajasTrait;

    private $fechaTrasplanteBolsa = "";
    private $fechaTrasplanteCampo = "";

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

        $this->fnconsultarRangosAlerta();

        $arrAlerta = $this->alerta($data);

        $evento            = $arrAlerta[0];
        $color             = $arrAlerta[1];
        $diasTranscurridos = $arrAlerta[2];

        if ($this['get_planta_madre']['get_trasplantes'] != null) {
            $this->registrosTrasplante($this['get_planta_madre']['get_trasplantes']);
        }

        $sumaCantidadBajas = $this->cantidadBajas($this['pro_id_lote'], ['ESQUEJES','CAMPO', 'BOLSA']);
        $cantidadTrasplarCampo = (($this['get_planta_madre']['pm_cantidad_semillas'] + $this['get_planta_madre']['pm_cantidad_esquejes']) - $sumaCantidadBajas);

        return [
            'pm_id'                      => $this['get_planta_madre']['pm_id'],
            'id_lote'                    => $this['pro_id_lote'],
            'fecha_propagacion'          => $this['pro_fecha'],
            'fecha_trasplante_bolsa'    => $this->fechaTrasplanteBolsa,
            'fecha_trasplante_Campo'    => $this->fechaTrasplanteCampo,
            'cantidad_trasplante_campo' => $cantidadTrasplarCampo,
            'estado_lote'                => $evento,
            'dias_transcurridos'         => $diasTranscurridos,
            'color'                      => $color
            ,
        ];
    }

    /**
     * Establece fecha de trasplante a bolsa y campo.
     *
     * @param array $trasplante
     * @return void
     */
    private function registrosTrasplante($trasplante){

        $this->fechaTrasplanteBolsa = "";
        $this->fechaTrasplanteCampo = "";

        if ($trasplante != null) {
            // Tiene dos trasplantes, bolsa y campo
            if (count($trasplante) == 2) {
                foreach ($trasplante as $trasplante) {
                    if ($trasplante['tp_tipo'] == 'bolsa') {
                        $this->fechaTrasplanteBolsa = $trasplante['tp_fecha'];
                    }else{
                        $this->fechaTrasplanteCampo = $trasplante['tp_fecha'];
                    }
                }
            }else if(count($trasplante) == 1){
                // Tiene solo trasplante bolsa
                if ($trasplante[0]['tp_tipo'] == 'bolsa') {
                    $this->fechaTrasplanteBolsa = $trasplante[0]['tp_fecha'];
                }
            }
        }
    }

}
