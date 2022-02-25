<?php

namespace App\Http\Controllers\Transplante;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransplanteBuscarRequest;
use App\Http\Requests\TransplanteStoreRequest;
use App\Models\PlantaMadre;
use App\Models\Propagacion;
use App\Models\Transplante;
use Illuminate\Http\Request;
use App\Traits\paginationTrait;
use App\Http\Resources\ListarTransplanteBolsaCollection;
use App\Models\Baja;

class TransplanteController extends Controller
{
    use paginationTrait;

    /**
     * Método que busca planta madre por un rango de fechas para mostrarlas en el datatable de transplantes a bolsa.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function buscar(TransplanteBuscarRequest $request) {
        // Se válida si envian los parámetros length
        if(!$request->filled('length')){
            $length = 15;
        }else{
            $length = $request->length;
        }

        $registros = Propagacion::select(['pro_id_lote', 'pro_fecha'])
            ->with([
                'getPlantaMadre' => function ($query) use ($request) {
                    $query->select([
                        'pm_id',
                        'pm_pro_id_lote',
                        'pm_fecha_esquejacion'
                    ])
                    ->with([
                        'getTransplante' => function ($query) use ($request){
                            $query->select([
                                'tp_pm_id',
                                'tp_fecha'
                            ]);
                        }]);
                }
            ]);

        if ($request->filled('fecha_inicial') && $request->filled('fecha_final')) {

            $registros = $registros->whereHas('getPlantaMadre', function ($query) use ($request) {
                $query->whereBetween('pm_fecha_esquejacion',[$request->fecha_inicial.' 00:00:00', $request->fecha_final.' 23:59:59']);
            });
        }

        $registros = $registros->BuscarTransplanteBolsa($request->buscar);

        // Consultas
        if ($request->filled('orderColumn') && $request->filled('order')) {

            $existeColumna = false;
            // Consulta Valor a buscar y ordenar registros ACS o DESC por una columna.
            switch ($request->orderColumn) {
                case 'id_lote':
                    $existeColumna = true;
                    $request->orderColumn = 'pro_id_lote';
                break;
                case 'fecha_propagacion':
                    $existeColumna = true;
                    $request->orderColumn = 'pro_fecha';
                break;
            }

            if ($existeColumna == true) {
                $registros = $registros->orderBy($request->orderColumn, $request->order);
            }
        }

        $registros = $registros->get();
        $registros = $registros->toArray();
        $registrosNuevos = ListarTransplanteBolsaCollection::collection($registros);

        if ($request->orderColumn == 'fecha_transplante') {
            $registrosNuevos = collect($registrosNuevos)->sortBy([
                ['fecha_transplante', strtolower($request->order)]
            ]);
        }

        $registros = $this->paginar($request,$registrosNuevos->all(), $length);
        return response()->json($registros, 200);
    }

    /**
     * Guarda transplante a bolsa o transplante a campo dependiendo del módulo.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TransplanteStoreRequest $request) {
        $plantaMadre = PlantaMadre::select('planta_madre.pm_id',
                                            'planta_madre.pm_pro_id_lote',
                                            'propagacion.pro_cantidad_plantas_madres',
                                            'propagacion.pro_cantidad_material')
            ->join('propagacion', 'planta_madre.pm_pro_id_lote', '=', 'propagacion.pro_id_lote')
            ->where('pm_id', $request->tp_pm_id)
            ->first();

        if (empty($plantaMadre)) {
            return response()->json([
                'error' => 'No existen resultados con el id especificado.',
                'code' => 404
            ], 404);
        }

        // Se Eliminan registro en caso de que exista en planta madre, para simular un update.
        Transplante::where('tp_pm_id', $plantaMadre->pm_id)->delete();

        Transplante::create([
            'tp_pm_id'          => $plantaMadre->pm_id,
            'tp_tipo'           => $request->tp_tipo,
            'tp_tipo_lote'      => $request->tp_tipo_lote,
            'tp_fecha'          => $request->tp_fecha." ".date('H:i:s'),
            'tp_ubicacion'      => $request->tp_ubicacion,
            'tp_cantidad_area'  => $request->tp_cantidad_area,
            'tp_estado'         => true,
        ]);

        return response()->json([
            'message'       => 'Datos Guardados.',
        ], 201);
    }

    /**
     * Muestra un registro.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $registro = PlantaMadre::select('planta_madre.pm_id',
                                        'planta_madre.pm_pro_id_lote',
                                        'planta_madre.pm_cantidad_semillas',
                                        'planta_madre.pm_cantidad_esquejes',
                                        'transplante.tp_fecha',
                                        'transplante.tp_tipo_lote',
                                        'transplante.tp_ubicacion',
                                        'transplante.tp_cantidad_area',
                                        'propagacion.pro_cantidad_plantas_madres')
            ->leftjoin('transplante', 'planta_madre.pm_id', '=', 'transplante.tp_pm_id')
            ->join('propagacion', 'planta_madre.pm_pro_id_lote', '=', 'propagacion.pro_id_lote')
            ->where('planta_madre.pm_id', $id)
            ->get();

        if ($registro->isEmpty()) {
            return response()->json([
                "error" => "No se encontraron resultados con el id especificado.",
                "code"  => 404
            ], 404);
        }

        // Obteniendo bajas del lote.
        $bajas = Baja::select(['bj_cantidad'])
            ->where('bj_fase_cultivo','bolsa')
            ->where('bj_pro_id_lote', $registro[0]->pm_pro_id_lote)
            ->get();

        $sumaCantidadBajas = 0;
        foreach ($bajas as $cantidadBaja) {
            $sumaCantidadBajas = $cantidadBaja->bj_cantidad + $sumaCantidadBajas;
        }

        $registro = $registro->map(function ($data) use ($sumaCantidadBajas){
            return [
                'tp_pm_id'                     => $data->pm_id,
                'tp_fecha'                     => $data->tp_fecha == '' || $data->tp_fecha == '0000-00-00 00:00:00' ?  '' : substr($data->tp_fecha,0,10), // Fecha transplante
                'cantidad_transplante_bolsa'   => (($data->pm_cantidad_semillas + $data->pm_cantidad_esquejes) - $sumaCantidadBajas), // Cantidad Sembrada en bolsa
                'tp_tipo_lote'                 => $data->tp_tipo_lote == '' ? '' : $data->tp_tipo_lote,       // Tipo Lote
                'tp_ubicacion'                 => $data->tp_ubicacion == '' ? '' : $data->tp_ubicacion, // Ubicación
                'tp_cantidad_area'             => $data->tp_cantidad_area == '' || $data->tp_cantidad_area == 0 ?  '' : $data->tp_cantidad_area
            ];
        });

        return response()->json([
            'data' => $registro[0],
        ], 200);
    }
}
