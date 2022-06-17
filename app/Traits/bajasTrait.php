<?php
namespace App\Traits;

use App\Models\Baja;
use App\Models\Parametros\FaseCultivo;

trait bajasTrait {

    /**
     * Obtiene cantidad bajas de un lote por las fases del cultivo.
     *
     * @param integer $id_lote
     * @return integer Cantidad bajas.
     */
    static function cantidadBajas($id_lote, $fasesCultivo = []) {

        $fasesCultivo = FaseCultivo::select('id')->whereIn('nombre', $fasesCultivo)->get();

        $id = "";
        foreach ($fasesCultivo as $faseCultivoId) {
            $id .= $faseCultivoId->id.",";
        }

        $idFaseCultivo = trim($id,","); // Eliminando coma al principio y final
        $vIdFasesCultivo = explode(',', $idFaseCultivo);

        // Obteniendo bajas del lote.
        $bajas = Baja::select(['bj_cantidad'])
            ->where('bj_pro_id_lote', $id_lote)
            ->whereIn('bj_fase_cultivo', $vIdFasesCultivo)
            ->get();

        $sumaCantidadBajas = 0;
        foreach ($bajas as $cantidadBaja) {
            $sumaCantidadBajas = $cantidadBaja->bj_cantidad + $sumaCantidadBajas;
        }

        return $sumaCantidadBajas;
    }
}
?>
