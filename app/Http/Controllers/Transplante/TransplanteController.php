<?php

namespace App\Http\Controllers\Transplante;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransplanteBuscarRequest;
use App\Http\Requests\TransplanteStoreRequest;
use App\Models\PlantaMadre;
use App\Models\Propagacion;
use App\Models\Transplante;
use Illuminate\Http\Request;

class TransplanteController extends Controller
{
    /**
     * Método que busca planta madre por un rango de fechas para mostrarlas en el datatable de transplantes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function buscar(TransplanteBuscarRequest $request)
    {
        // Se válida si envian los parámetros length
        if(!$request->filled('length')){
            $length = 15;
        }else{
            $length = $request->length;
        }

        $registros = PlantaMadre::select([
                                            'planta_madre.pm_pro_id_lote as id_lote',
                                            'planta_madre.pm_id',
                                            'propagacion.pro_fecha as fecha_propagacion',
                                            'transplante.tp_fecha as fecha_transplante'
                                        ])
            ->join('propagacion', 'planta_madre.pm_pro_id_lote', '=', 'propagacion.pro_id_lote')
            ->leftjoin('transplante', 'planta_madre.pm_id', '=', 'transplante.tp_pm_id')
            ->BuscarTransplante($request->buscar);

        // Consultas
        if ($request->filled('orderColumn') && $request->filled('order')) {

            $existeColumna = false;
            // Consulta Valor a buscar y ordenar registros ACS o DESC por una columna.
            switch ($request->orderColumn) {
                case 'id_lote':
                    $existeColumna = true;
                    $request->orderColumn = 'pm_pro_id_lote';
                break;
                case 'fecha_propagacion':
                    $existeColumna = true;
                    $request->orderColumn = 'pro_fecha';
                break;
                case 'fecha_transplante':
                    $existeColumna = true;
                    $request->orderColumn = 'tp_fecha';
                break;
            }

            if ($existeColumna == true) {
                $registros = $registros->orderBy($request->orderColumn, $request->order);
            }
        }

        // Consulta Valor a buscar.
        $registros = $registros->whereBetween('pm_fecha_esquejacion',[$request->fecha_inicial.' 00:00:00', $request->fecha_final.' 23:59:59'])
            ->paginate($length);

        $registros->getCollection()->transform(function($data, $key) {
            return [
                'pm_id'                         => $data->pm_id,              // ( Id lote )
                'id_lote'                       => $data->id_lote,              // ( Id lote )
                'fecha_propagacion'             => $data->fecha_propagacion,    // (Fecha de Propagación)
                'fecha_transplante'             => $data->fecha_transplante,    // (Fecha transplante Bolsa)
                'accion'                        => 'Pendiente',
            ];
        });

        return response()->json($registros, 200);
    }

    /**
     * Guarda transplante a bolsa o transplante a campo dependiendo del módulo.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TransplanteStoreRequest $request)
    {
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

        $cantidadMalas    = $plantaMadre->pro_cantidad_material - $plantaMadre->pro_cantidad_plantas_madres;
        $porcentajeMalas  = round(($cantidadMalas * 100) / $plantaMadre->pro_cantidad_material,2);
        $porcentajeBuenas = round( 100 - $porcentajeMalas, 2);

        return response()->json([
            'message'       => 'Datos Guardados.',
            'notificacion'  =>  [
                'cantidad_malas'    => $cantidadMalas,
                'porcentaje_malas'  => $porcentajeMalas.' %',
                'porcentaje_buenas' => $porcentajeBuenas.' %',
            ]
        ], 201);
    }

    /**
     * Muestra un registro.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $registro = PlantaMadre::select('planta_madre.pm_id',
                                        'transplante.tp_fecha',
                                        'transplante.tp_tipo_lote',
                                        'transplante.tp_ubicacion',
                                        'transplante.tp_cantidad_area',
                                        'propagacion.pro_cantidad_plantas_madres')
            ->leftjoin('transplante', 'planta_madre.pm_id', '=', 'transplante.tp_pm_id')
            ->join('propagacion', 'planta_madre.pm_pro_id_lote', '=', 'propagacion.pro_id_lote')
            ->where('planta_madre.pm_id', $id)
            ->get()
            ->map(function ($data) {
                return [
                    'tp_pm_id'                => $data->pm_id,
                    'tp_fecha'                => $data->tp_fecha == '' ?  '' : substr($data->tp_fecha,0,10), // Fecha transplante
                    'cantidad_buenas'         => $data->pro_cantidad_plantas_madres,                         // PLantans buenas
                    'tp_tipo_lote'            => $data->tp_tipo_lote == '' ? '' : $data->tp_tipo_lote,       // Tipo Lote
                    'tp_ubicacion'            => $data->tp_ubicacion == '' ? '' : $data->tp_ubicacion, // Ubicación
                    'tp_cantidad_area'        => $data->tp_cantidad_area == '' || $data->tp_cantidad_area == 0 ?  '' : $data->tp_cantidad_area
                ];
            });

        if ($registro->isEmpty()) {
            return response()->json([
                "error" => "No se encontraron resultados con el id especificado.",
                "code"  => 404
            ], 404);
        }

        return response()->json([
            'data' => $registro[0],
        ], 200);
    }
}
