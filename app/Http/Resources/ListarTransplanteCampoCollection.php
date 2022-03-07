<?php

namespace App\Http\Resources;

use App\Models\Baja;
use App\Traits\alertaTrait;
use App\Traits\bajasTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ListarTransplanteCampoCollection extends ResourceCollection
{
    use alertaTrait;
    use bajasTrait;

    private $fechaTransplanteBolsa = "";
    private $fechaTransplanteCampo = "";

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

        if ($this['get_planta_madre']['get_transplantes'] != null) {
            $this->registrosTransplante($this['get_planta_madre']['get_transplantes']);
        }

        $sumaCantidadBajas = $this->cantidadBajas($this['pro_id_lote'], ['esquejes','campo', 'bolsa']);
        $cantidadTransplarCampo = (($this['get_planta_madre']['pm_cantidad_semillas'] + $this['get_planta_madre']['pm_cantidad_esquejes']) - $sumaCantidadBajas);

        return [
            'pm_id'                      => $this['get_planta_madre']['pm_id'],
            'id_lote'                    => $this['pro_id_lote'],
            'fecha_propagacion'          => $this['pro_fecha'],
            'fecha_transplante_bolsa'    => $this->fechaTransplanteBolsa,
            'fecha_transplante_Campo'    => $this->fechaTransplanteCampo,
            'cantidad_transplante_campo' => $cantidadTransplarCampo,
            'estado_lote'                => $evento,
            'dias_transcurridos'         => $diasTranscurridos,
            'color'                      => $color
            ,
        ];
    }

    /**
     * Establece fecha de transplante a bolsa y campo.
     *
     * @param array $transplante
     * @return void
     */
    private function registrosTransplante($transplante){

        $this->fechaTransplanteBolsa = "";
        $this->fechaTransplanteCampo = "";

        if ($transplante != null) {
            // Tiene dos transplantes, bolsa y campo
            if (count($transplante) == 2) {
                foreach ($transplante as $transplante) {
                    if ($transplante['tp_tipo'] == 'bolsa') {
                        $this->fechaTransplanteBolsa = $transplante['tp_fecha'];
                    }else{
                        $this->fechaTransplanteCampo = $transplante['tp_fecha'];
                    }
                }
            }else if(count($transplante) == 1){
                // Tiene solo transplante bolsa
                if ($transplante[0]['tp_tipo'] == 'bolsa') {
                    $this->fechaTransplanteBolsa = $transplante[0]['tp_fecha'];
                }
            }
        }
    }

}
