<?php
namespace App\Traits;

use App\Models\Baja;

trait bajasTrait {

    /**
     * Obtiene cantidad bajas de un lote por las fases del cultivo.
     *
     * @param integer $id_lote
     * @return integer Cantidad bajas.
     */
    static function cantidadBajas($id_lote, $fasesCultivo = []) {

        // Obteniendo bajas del lote.
        $bajas = Baja::select(['bj_cantidad'])
            ->whereIn('bj_fase_cultivo', $fasesCultivo)
            ->where('bj_pro_id_lote', $id_lote)
            ->get();

        $sumaCantidadBajas = 0;
        foreach ($bajas as $cantidadBaja) {
            $sumaCantidadBajas = $cantidadBaja->bj_cantidad + $sumaCantidadBajas;
        }

        return $sumaCantidadBajas;
    }
}
?>
