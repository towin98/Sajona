<?php

namespace App\Http\Controllers\PlantaMadre;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlantaMadreBuscarRequest;
use App\Http\Requests\PlantaMadreStoreRequest;
use App\Models\PlantaMadre;
use App\Models\Propagacion;
use App\Traits\alertaTrait;

class PlantaMadreController extends Controller
{

    use alertaTrait;
    /**
     * Método que busca lotes de propagación por un rango de fechas.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function buscarLotes(PlantaMadreBuscarRequest $request)
    {
        // Se válida si envian los parámetros length
        if(!$request->filled('length')){
            $length = 10;
        }else{
            $length = $request->length;
        }

        $registros = Propagacion::select('*')
            ->with('getPlantaMadre')
            ->BuscarPLantaMadre($request->buscar);

        // Consultas
        if ($request->filled('orderColumn') && $request->filled('order')) {
            // Consulta Valor a buscar y ordenar registros ACS o DESC por una columna.
            switch ($request->orderColumn) {
                case 'pm_fecha_esquejacion':
                    $registros = $registros->leftJoin('planta_madre', 'planta_madre.pm_pro_id_lote', '=', 'propagacion.pro_id_lote')
                        ->orderBy('planta_madre.pm_fecha_esquejacion', $request->order);
                break;
                default:
                    $registros = $registros->orderBy($request->orderColumn, $request->order);
                break;
            }
        }

        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $registros = $registros->whereBetween('pro_fecha',[$request->fecha_inicio.' 00:00:00', $request->fecha_fin.' 23:59:59']);
        }

        $registros = $registros->paginate($length);

        $registros->getCollection()->transform(function($data, $key) {

            $arrAlerta = $this->alerta($data);

            $evento            = $arrAlerta[0];
            $color             = $arrAlerta[1];
            $diasTranscurridos = $arrAlerta[2];

            return [
                'pro_id_lote'                   => $data->pro_id_lote,              // ( Id lote )
                'pro_fecha'                     => $data->pro_fecha,                // ( Fecha propagación )
                'pm_fecha_esquejacion'          => optional($data->getPlantaMadre)->pm_fecha_esquejacion, // plantas madres ( Fecha Transplante )
                'pro_cantidad_plantas_madres'   => $data->pro_cantidad_plantas_madres,
                'estado_lote'                   => $evento,
                'dias_transcurridos'            => $diasTranscurridos,
                'color'                         => $color
            ];
        });

        return response()->json($registros, 200);
    }

    /**
     * Método que busca por id del lote plantas madres y actualiza registro.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PlantaMadreStoreRequest $request)
    {
        $plantaMadre = PlantaMadre::where('pm_pro_id_lote', $request->pm_pro_id_lote);

        if (count($plantaMadre->get()) == 0) {
            $plantaMadre->create([
                'pm_pro_id_lote'        => $request->pm_pro_id_lote,
                'pm_fecha_esquejacion'  => $request->pm_fecha_esquejacion." ".date('H:i:s'),
                'pm_cantidad_semillas'  => $request->pm_cantidad_semillas,
                'pm_cantidad_esquejes'  => $request->pm_cantidad_esquejes,
                'pm_estado'             => true,
            ]);
        }else{
            $plantaMadre->update([
                'pm_pro_id_lote'        => $request->pm_pro_id_lote,
                'pm_fecha_esquejacion'  => $request->pm_fecha_esquejacion." ".date('H:i:s'),
                'pm_cantidad_semillas'  => $request->pm_cantidad_semillas,
                'pm_cantidad_esquejes'  => $request->pm_cantidad_esquejes,
                'pm_estado'             => true,
            ]);
        }

        return response()->json([
            'message' => 'Datos Guardados.',
        ], 201);
    }

    /**
     * Busca y Muestra un lote en especifico.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Propagacion $Propagacion)
    {
        $plantaMadre = PlantaMadre::where('pm_pro_id_lote', $Propagacion->pro_id_lote)->get();

        // Armando collection con data de propagacion y de plantas madres.
        $esquejeSemilla = collect([
            'pro_id_lote'                 => $Propagacion->pro_id_lote,
            'pro_cantidad_plantas_madres' => $Propagacion->pro_cantidad_plantas_madres,
            'pm_fecha_esquejacion'        => $plantaMadre->isEmpty() ? '' : substr($plantaMadre[0]->pm_fecha_esquejacion,0,10) ,
            'pm_cantidad_esquejes'        => $plantaMadre->isEmpty() ? 0 : $plantaMadre[0]->pm_cantidad_esquejes,
            'pm_cantidad_semillas'        => $plantaMadre->isEmpty() ? 0 : $plantaMadre[0]->pm_cantidad_semillas
        ]);

        return response()->json([
            'data' => $esquejeSemilla,
        ], 200);
    }
}
