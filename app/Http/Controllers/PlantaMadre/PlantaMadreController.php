<?php

namespace App\Http\Controllers\PlantaMadre;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlantaMadreBuscarRequest;
use App\Http\Requests\PlantaMadreStoreRequest;
use App\Models\PlantaMadre;
use App\Models\Propagacion;

class PlantaMadreController extends Controller
{
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

        // Consultas
        if ($request->filled('orderColumn') && $request->filled('order')) {

            // Consulta Valor a buscar y ordenar registros ACS o DESC por una columna.
            $registros = Propagacion::select('*')
                ->Buscar($request->buscar)
                ->orderBy($request->orderColumn, $request->order)
                ->whereBetween('pro_fecha',[ $request->fecha_inicio, $request->fecha_fin])
                ->paginate($length);
        }else{

            // Consulta Valor a buscar.
            $registros = Propagacion::select('*')
                ->Buscar($request->buscar)
                ->whereBetween('pro_fecha',[$request->fecha_inicio.' 00:00:00', $request->fecha_fin.' 23:59:59'])
                ->paginate($length);
        }

        return response()->json([
            'data' => $registros,
        ], 200);
    }

    /**
     * Método que busca por id del lote plantas madres y actualiza registro.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PlantaMadreStoreRequest $request)
    {
        // Se Eliminan registro en caso de que exista en planta madre, para simular un update.
        PlantaMadre::where('pm_pro_id_lote', $request->pm_pro_id_lote)->delete();

        PlantaMadre::create([
            'pm_pro_id_lote'        => 100,
            'pm_fecha_esquejacion'  => $request->pm_fecha_esquejacion,
            'pm_cantidad_semillas'  => $request->pm_cantidad_semillas,
            'pm_cantidad_esquejes'  => $request->pm_cantidad_esquejes,
            'pm_estado'             => true,
        ]);

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
            'pro_cantidad_plantas_madres' => $Propagacion->pro_cantidad_plantas_madres,
            'pm_cantidad_esquejes'        => $plantaMadre->isEmpty() ? 0 : $plantaMadre[0]->pm_cantidad_esquejes,
            'pm_cantidad_semillas'        => $plantaMadre->isEmpty() ? 0 : $plantaMadre[0]->pm_cantidad_semillas
        ]);

        return response()->json([
            'data' => $esquejeSemilla,
        ], 200);
    }
}
