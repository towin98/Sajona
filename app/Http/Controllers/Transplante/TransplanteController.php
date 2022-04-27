<?php

namespace App\Http\Controllers\Transplante;

use App\Traits\bajasTrait;
use App\Models\PlantaMadre;
use App\Models\Propagacion;
use App\Models\Transplante;
use App\Traits\alertaTrait;
use Illuminate\Http\Request;
use App\Traits\commonsTrait;
use App\Traits\paginationTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\TransplanteStoreRequest;
use App\Http\Requests\TransplanteBuscarRequest;
use App\Http\Requests\TransplanteCampoStoreRequest;
use App\Http\Resources\ListarTransplanteCampoCollection;
use Illuminate\Auth\Access\AuthorizationException;

class TransplanteController extends Controller
{
    use paginationTrait;
    use bajasTrait;
    use alertaTrait;
    use commonsTrait;

    public function __construct()
    {
        $this->middleware(['permission:LISTAR'])->only(['buscarTransplanteBolsa', 'buscarTransplanteCampo']);
        $this->middleware(['permission:CREAR|EDITAR'])->only(['storeTransplanteBolsa', 'storeTransplanteCampo']);
        $this->middleware(['permission:VER'])->only(['showTransplanteBolsa', 'showTransplanteCampo']);
    }

    /**
     * Método que busca planta madre por un rango de fechas para mostrarlas en el datatable de transplantes a bolsa.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function buscarTransplanteBolsa(TransplanteBuscarRequest $request) {

        // Se válida si envian los parámetros length y start.
        if($request->has(['length', 'start'])){
            $length = $request->length;
            $start  = $request->start;
        }else{
            $length = 50;
            $start  = 0;
        }


        $registros = DB::table('propagacion')
            ->select(
                        'planta_madre.pm_id',
                        'propagacion.pro_id_lote',
                        'propagacion.pro_fecha',
                        'planta_madre.pm_fecha_esquejacion',
                        'transplante.tp_fecha'
                    )
            ->join('planta_madre', 'planta_madre.pm_pro_id_lote', '=', 'propagacion.pro_id_lote')
            ->leftJoin('transplante', function ($join) {
                $join->on('transplante.tp_pm_id', '=', 'planta_madre.pm_id')
                    ->where('transplante.tp_tipo', '=', 'bolsa');
            });

        // Buscando por rango de fechas si digitan.
        if ($request->filled('fecha_inicial') && $request->filled('fecha_final')) {
            $registros = $registros->whereBetween('pro_fecha',[$request->fecha_inicial.' 00:00:00', $request->fecha_final.' 23:59:59']);
        }

        //Busqueda por campos.
        if ($request->filled('buscar')) {
            $registros = $registros
                ->where('propagacion.pro_id_lote', 'LIKE', "%$request->buscar%")
                ->orWhere('propagacion.pro_fecha', 'LIKE', "%$request->buscar%")
                ->orWhere('transplante.tp_fecha', 'LIKE', "%$request->buscar%");
        }

        $totalRegistros = $registros->count();

        // Ordenamiento
        switch ($request->orderColumn) {
            case 'id_lote':
                $registros = $registros->orderBy('propagacion.pro_id_lote', $request->order);
            break;
            case 'pro_fecha':
                $registros = $registros->orderBy('propagacion.pro_fecha', $request->order);
            break;
            case 'tp_fecha':
                $registros = $registros->orderBy('transplante.tp_fecha', $request->order);
            break;
        }

        $registros = $registros
            ->skip($start)
            ->take($length)
            ->get();

        $filtrados = $registros->count();

        $registros = $registros->map(function ($value, $key){
            // Se armana data como la requiere, tipo object.
            $data = new Request([
                'pro_fecha' => $value->pro_fecha,
                'getPlantaMadre' => new Request([
                    'pm_id' => $value->pm_id
                ])
            ]);

            $arrAlerta = $this->alerta($data);

            $evento            = $arrAlerta[0];
            $color             = $arrAlerta[1];
            $diasTranscurridos = $arrAlerta[2];

            return [
                'pm_id'                    => $value->pm_id,
                'id_lote'                  => $value->pro_id_lote,
                'fecha_propagacion'        => $value->pro_fecha,
                'pm_fecha_esquejacion'     => $value->pm_fecha_esquejacion,
                'tp_fecha'                 => $value->tp_fecha,
                'accion'                   => $value->tp_fecha == null || $value->tp_fecha == '0000-00-00 00:00:00' ? 'Pendiente' : 'Finalizado',
                'estado_lote'              => $evento,
                'dias_transcurridos'       => $diasTranscurridos,
                'color'                    => $color
            ];
        });

        return response()->json([
            'data'      => $registros,
            'filtrados' => $filtrados,
            'total'     => $totalRegistros,
        ], 200);

    }

    /**
     * Guarda o Actualiza transplante a Bolsa.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeTransplanteBolsa(TransplanteStoreRequest $request) {
        $plantaMadre = PlantaMadre::select('planta_madre.pm_id',
                                            'planta_madre.pm_pro_id_lote',
                                            'propagacion.pro_cantidad_plantas_madres',
                                            'propagacion.pro_cantidad_material')
            ->join('propagacion', 'planta_madre.pm_pro_id_lote', '=', 'propagacion.pro_id_lote')
            ->where('pm_id', $request->tp_pm_id)
            ->first();

        if (empty($plantaMadre)) {
            return response()->json([
                'errors' => 'No existen resultados con el id especificado.',
                'code' => 404
            ], 404);
        }

        $transplanteBolsa = Transplante::where([
                'tp_pm_id' => $plantaMadre->pm_id,
                'tp_tipo'  => 'bolsa'
        ]);

        if ($transplanteBolsa->first()) {
            if (!$this->fnVerificaPermisoUsuario('EDITAR')) {
                throw new AuthorizationException;
            }
        }else{
            if (!$this->fnVerificaPermisoUsuario('CREAR')) {
                throw new AuthorizationException;
            }
        }

        // Se Eliminan registro en caso de que exista en planta madre, para simular un update.
        $transplanteBolsa->delete();

        Transplante::create([
            'tp_pm_id'          => $plantaMadre->pm_id,
            'tp_tipo'           => 'bolsa',
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
     * Muestra un registro de transplante a bolsa.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showTransplanteBolsa($id) {
        $registro = DB::table('planta_madre')->select('planta_madre.pm_id',
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
        $sumaCantidadBajas = $this->cantidadBajas($registro[0]->pm_pro_id_lote, ['esquejes','bolsa']);

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

    /* -------------- Metodos transplante a Campo  ------------------ */

    /**
     * Método que busca planta madre por un rango de fechas para mostrarlas en el datatable de transplantes a bolsa.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function buscarTransplanteCampo(TransplanteBuscarRequest $request) {

        // Se válida si envian los parámetros length y start.
        if($request->has(['length', 'start'])){
            $length = $request->length;
            $start  = $request->start;
        }else{
            $length = 15;
            $start  = 0;
        }

        $registros = Propagacion::select(['pro_id_lote', 'pro_fecha'])
            ->with([
                'getPlantaMadre' => function ($query) use ($request) {
                    $query->select([
                        'pm_id',
                        'pm_pro_id_lote',
                        'pm_fecha_esquejacion',
                        'pm_cantidad_semillas',
                        'pm_cantidad_esquejes'
                    ])
                    ->with([
                        'getTransplantes' => function ($query) use ($request){
                            $query->select([
                                'tp_pm_id',
                                'tp_fecha',
                                'tp_tipo'
                            ]);
                        }
                    ]);
                }
            ])
            ->whereHas('getPlantaMadre');

        // Buscando por rango de fechas si digitan.
        if ($request->filled('fecha_inicial') && $request->filled('fecha_final')) {

            $registros = $registros->whereHas('getPlantaMadre', function ($query) use ($request) {
                $query->whereBetween('pro_fecha',[$request->fecha_inicial.' 00:00:00', $request->fecha_final.' 23:59:59']);
            });
        }

        $registros = $registros->whereHas('getPlantaMadre', function ($query) use ($request) {
                $query->whereHas('getTransplantes', function ($query) use ($request) {
                    $query->where('tp_tipo', 'bolsa')
                        ->orWhere('tp_tipo', 'campo');
                });
            })
            ->BuscarTransplanteCampo($request->buscar);

        // consulta para saber cuantos registros hay.
        $totalRegistros = $registros->count();

        $registros = $registros
            ->skip($start)
            ->take($length)
            ->get()
            ->toArray();

        $registros = ListarTransplanteCampoCollection::collection($registros);

        // Ordenamiento
        if ($request->filled('orderColumn') && $request->filled('order')) {
            switch ($request->orderColumn) {
                case 'id_lote':
                case 'fecha_transplante_Campo':
                case 'fecha_transplante_bolsa':
                case 'fecha_propagacion':
                    $registros = collect($registros)->sortBy([
                        [$request->orderColumn, strtolower($request->order)]
                    ]);
                break;
            }
        }

        // $registros = $registros->slice($start,$length);
        // $data      = $registros->values();

        return response()->json([
            'data'      => $registros,
            'filtrados' => $registros->count(),
            'total'     => $totalRegistros,
        ], 200);
    }

    /**
     * Muestra fecha transplante a campo y cantidad transplante bolsa, (valor automatico)
     *
     * @param integer $tp_pm_id id de planta madre
     * @return \Illuminate\Http\Response
     */
    public function showTransplanteCampo($tp_pm_id) {

        $data = [];

        $plantaMadre = PlantaMadre::select([
                'pm_pro_id_lote',
                'pm_cantidad_semillas',
                'pm_cantidad_esquejes'
            ])
            ->where('pm_id', $tp_pm_id)
            ->first();

        if ($plantaMadre) {

            $sumaCantidadBajas = $this->cantidadBajas($plantaMadre->pm_pro_id_lote, ['esquejes','campo', 'bolsa']);

            // Calculando cantidad transplante a campo.
            $data['pm_pro_id_lote'] = $plantaMadre->pm_pro_id_lote;
            $data['cantidad_transplante_campo'] = (($plantaMadre->pm_cantidad_semillas + $plantaMadre->pm_cantidad_esquejes) - $sumaCantidadBajas);

            $transplanteBolsa = Transplante::select('tp_fecha')
                ->where('tp_pm_id', $tp_pm_id)
                ->where('tp_tipo', 'campo')
                ->first();

            // Tiene transplante a bolsa
            if($transplanteBolsa){
                $data['tp_fecha'] = substr($transplanteBolsa->tp_fecha,0,10) ;
            }else{
                $data['tp_fecha'] = "";
            }
        }

        return response()->json([
            'data' => $data,
        ], 200);
    }

    /**
     * Se crea transplante a campo si no existe, de lo contrario se actualiza solo la fecha de trasnplante.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function storeTransplanteCampo(TransplanteCampoStoreRequest $request){

        $plantaMadre = PlantaMadre::select([
                'pm_id',
            ])
            ->where('pm_pro_id_lote', $request->id_lote)
            ->first();
        if ($plantaMadre) {

            $transplanteCampo = Transplante::select(['tp_id'])
                ->where('tp_pm_id', $plantaMadre->pm_id)
                ->where('tp_tipo', 'campo');

            // Consultando Transplante a bolsa para crear registro de transplante a campo con los mismos registros
            // de 'tp_tipo_lote','tp_ubicacion', 'tp_cantidad_area'
            $transplanteBolsa = Transplante::select([
                    'tp_tipo_lote',
                    'tp_ubicacion',
                    'tp_cantidad_area'
                ])
                ->where('tp_pm_id', $plantaMadre->pm_id)
                ->where('tp_tipo', 'bolsa')
                ->first();

            // Si no existe transplante a campo se crea
            if (count($transplanteCampo->get()) == 0) {
                $transplanteCampo->create([
                    'tp_pm_id'          => $plantaMadre->pm_id,
                    'tp_tipo'           => 'campo',
                    'tp_tipo_lote'      => $transplanteBolsa->tp_tipo_lote,
                    'tp_fecha'          => $request->tp_fecha." ".date('H:i:s'),
                    'tp_ubicacion'      => $transplanteBolsa->tp_ubicacion,
                    'tp_cantidad_area'  => $transplanteBolsa->tp_cantidad_area,
                    'tp_estado'         => true
                ]);
            }else{
                // Se actualiza transplante a campo.
                $transplanteCampo->update([
                    'tp_fecha'          => $request->tp_fecha." ".date('H:i:s'),
                ]);
            }
        }else{
            return response()->json([
                'errors' => "No existen resultados con el id del lote[$request->id_lote] especificado.",
                'code' => 404
            ], 404);
        }

        return response()->json([
            'message' => 'Datos Guardados.',
        ], 201);
    }
}
