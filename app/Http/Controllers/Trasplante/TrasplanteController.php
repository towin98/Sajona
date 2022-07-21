<?php

namespace App\Http\Controllers\Trasplante;

use Exception;
use App\Traits\bajasTrait;
use App\Models\PlantaMadre;
use App\Models\Propagacion;
use App\Models\Trasplante;
use App\Traits\alertaTrait;
use Illuminate\Http\Request;
use App\Traits\commonsTrait;
use App\Traits\paginationTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\TrasplanteStoreRequest;
use App\Http\Requests\TrasplanteBuscarRequest;
use App\Http\Requests\TrasplanteCampoStoreRequest;
use App\Http\Resources\ListarTrasplanteCampoCollection;
use App\Models\Cosecha;
use Illuminate\Auth\Access\AuthorizationException;

class TrasplanteController extends Controller
{
    use paginationTrait;
    use bajasTrait;
    use alertaTrait;
    use commonsTrait;

    public function __construct()
    {
        $this->middleware(['permission:LISTAR'])->only(['buscarTrasplanteBolsa', 'buscarTrasplanteCampo']);
        $this->middleware(['permission:CREAR|EDITAR'])->only(['storeTrasplanteBolsa', 'storeTrasplanteCampo']);
        $this->middleware(['permission:VER'])->only(['showTrasplanteBolsa', 'showTrasplanteCampo']);
        $this->middleware(['permission:ELIMINAR'])->only(['deleteTrasplanteCampo']);
    }

    /**
     * Método que busca planta madre por un rango de fechas para mostrarlas en el datatable de trasplantes a bolsa.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function buscarTrasplanteBolsa(TrasplanteBuscarRequest $request) {

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
                        'trasplante.tp_fecha'
                    )
            ->join('planta_madre', 'planta_madre.pm_pro_id_lote', '=', 'propagacion.pro_id_lote')
            ->leftJoin('trasplante', function ($join) {
                $join->on('trasplante.tp_pm_id', '=', 'planta_madre.pm_id')
                    ->where('trasplante.tp_tipo', '=', 'bolsa')
                    ->where('trasplante.tp_estado',1);
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
                ->orWhere('trasplante.tp_fecha', 'LIKE', "%$request->buscar%");
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
                $registros = $registros->orderBy('trasplante.tp_fecha', $request->order);
            break;
        }

        $registros = $registros
            ->skip($start)
            ->take($length)
            ->get();

        $filtrados = $registros->count();

        // Requerido, consultando rango de trasplantes.
        $this->fnconsultarRangosAlerta();

        $registros = $registros->map(function ($value, $key){
            // Se armana data como la requiere, tipo object.
            $data = new Request([
                'pro_id_lote'    => $value->pro_id_lote,
                'pro_fecha'      => $value->pro_fecha,
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
     * Guarda o Actualiza trasplante a Bolsa.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeTrasplanteBolsa(TrasplanteStoreRequest $request) {
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

        $trasplanteBolsa = Trasplante::where([
                                                'tp_pm_id'  => $plantaMadre->pm_id,
                                                'tp_tipo'   => 'bolsa',
                                                'tp_estado' => 1
                                            ]);

        if ($trasplanteBolsa->first()) {
            if (!$this->fnVerificaPermisoUsuario('EDITAR')) {
                throw new AuthorizationException;
            }

            // CONSULTANDO SI EL LOTE TIENE TRASPLANTE A CAMPO, NO SE PERMITA EDITAR.
            $trasplanteCampo = Trasplante::select('tp_id')
                ->where([
                    'tp_pm_id'  => $plantaMadre->pm_id,
                    'tp_tipo'   => 'campo',
                    'tp_estado' => 1
                ])
                ->first();
            if ($trasplanteCampo) {
                return response()->json([
                    'message' => 'Error de Validación de Datos.',
                    'errors'  => "No se puede editar trasplante a bolsa, porque el lote $plantaMadre->pm_pro_id_lote ya tiene trasplante a bolsa."
                ], 409);
            }

            try {
                $trasplanteBolsa->update([
                    'tp_tipo_lote'      => $request->tp_tipo_lote,
                    'tp_fecha'          => $request->tp_fecha." ".date('H:i:s'),
                    'tp_ubicacion'      => $request->tp_ubicacion,
                    'tp_cantidad_area'  => $request->tp_cantidad_area,
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Acción no permitida',
                    'errors'  => "Error al actualizar trasplante a bolsa."
                ], 500);
            }
        }else{
            if (!$this->fnVerificaPermisoUsuario('CREAR')) {
                throw new AuthorizationException;
            }

            try {
                Trasplante::create([
                    'tp_pm_id'          => $plantaMadre->pm_id,
                    'tp_tipo'           => 'bolsa',
                    'tp_tipo_lote'      => $request->tp_tipo_lote,
                    'tp_fecha'          => $request->tp_fecha." ".date('H:i:s'),
                    'tp_ubicacion'      => $request->tp_ubicacion,
                    'tp_cantidad_area'  => $request->tp_cantidad_area,
                    'tp_estado'         => true,
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'message' => 'Error inesperado.',
                    'errors'  => "Error al guardar trasplante a bolsa."
                ], 500);
            }
        }

        return response()->json([
            'message'       => 'Datos Guardados.',
        ], 201);
    }

    /**
     * Muestra un registro de trasplante a bolsa.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showTrasplanteBolsa($id) {
        $registro = DB::table('planta_madre')->select('planta_madre.pm_id',
                                        'planta_madre.pm_pro_id_lote',
                                        'planta_madre.pm_cantidad_semillas',
                                        'planta_madre.pm_cantidad_esquejes',
                                        'trasplante.tp_fecha',
                                        'trasplante.tp_tipo_lote',
                                        'trasplante.tp_ubicacion',
                                        'trasplante.tp_cantidad_area',
                                        'propagacion.pro_cantidad_plantas_madres')
            ->leftJoin('trasplante', function ($join) {
                $join->on('planta_madre.pm_id', '=', 'trasplante.tp_pm_id')
                    ->where('trasplante.tp_estado',1);
            })
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
        $sumaCantidadBajas = $this->cantidadBajas($registro[0]->pm_pro_id_lote, ['ESQUEJES','BOLSA']);

        $registro = $registro->map(function ($data) use ($sumaCantidadBajas){
            return [
                'tp_pm_id'                     => $data->pm_id,
                'tp_fecha'                     => $data->tp_fecha == '' || $data->tp_fecha == '0000-00-00 00:00:00' ?  '' : substr($data->tp_fecha,0,10), // Fecha trasplante
                'cantidad_trasplante_bolsa'   => (($data->pm_cantidad_semillas + $data->pm_cantidad_esquejes) - $sumaCantidadBajas), // Cantidad Sembrada en bolsa
                'tp_tipo_lote'                 => $data->tp_tipo_lote == '' ? '' : $data->tp_tipo_lote,       // Tipo Lote
                'tp_ubicacion'                 => $data->tp_ubicacion == '' ? '' : $data->tp_ubicacion, // Ubicación
                'tp_cantidad_area'             => $data->tp_cantidad_area == '' || $data->tp_cantidad_area == 0 ?  '' : $data->tp_cantidad_area
            ];
        });

        return response()->json([
            'data' => $registro[0],
        ], 200);
    }

    /* -------------- Metodos trasplante a Campo  ------------------ */

    /**
     * Método que busca planta madre por un rango de fechas para mostrarlas en el datatable de trasplantes a bolsa.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function buscarTrasplanteCampo(TrasplanteBuscarRequest $request) {

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
                        'getTrasplantes' => function ($query) use ($request){
                            $query->select([
                                'tp_id',
                                'tp_pm_id',
                                'tp_fecha',
                                'tp_tipo',
                                'tp_estado'
                            ])
                            ->where('tp_estado',1);
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

        $registros = $registros->BuscarTrasplanteCampo($request->buscar);
        if ($request->filled('orderColumn') && $request->filled('order')) {
            switch ($request->orderColumn) {
                case 'id_lote':
                    $registros = $registros->orderBy('pro_id_lote', strtoupper($request->order));
                case 'fecha_propagacion':
                    $registros = $registros->orderBy('pro_fecha', strtoupper($request->order));
                break;
            }
        }

        // consulta para saber cuantos registros hay.
        $totalRegistros = $registros->count();

        $registros = $registros
            ->skip($start)
            ->take($length)
            ->get()
            ->toArray();
        $registros = ListarTrasplanteCampoCollection::collection($registros);

        // Ordenamiento
        if ($request->filled('orderColumn') && $request->filled('order')) {
            switch ($request->orderColumn) {
                case 'id_lote':
                case 'fecha_trasplante_Campo':
                case 'fecha_trasplante_bolsa':
                case 'fecha_propagacion':
                    $registros = collect($registros)->sortBy([
                        [$request->orderColumn, strtolower($request->order)]
                    ]);
                break;
            }
        }

        return response()->json([
            'data'      => $registros,
            'filtrados' => $registros->count(),
            'total'     => $totalRegistros,
        ], 200);
    }

    /**
     * Muestra fecha trasplante a campo y cantidad trasplante campo, (valor automatico)
     *
     * @param integer $tp_pm_id id de planta madre
     * @return \Illuminate\Http\Response
     */
    public function showTrasplanteCampo($tp_pm_id) {

        $data = [];

        $plantaMadre = PlantaMadre::select([
                'pm_pro_id_lote',
                'pm_cantidad_semillas',
                'pm_cantidad_esquejes'
            ])
            ->where('pm_id', $tp_pm_id)
            ->first();

        if ($plantaMadre) {

            $sumaCantidadBajas = $this->cantidadBajas($plantaMadre->pm_pro_id_lote, ['ESQUEJES','BOLSA','CAMPO']);

            // Calculando cantidad trasplante a campo.
            $data['pm_pro_id_lote'] = $plantaMadre->pm_pro_id_lote;
            $data['cantidad_trasplante_campo'] = (($plantaMadre->pm_cantidad_semillas + $plantaMadre->pm_cantidad_esquejes) - $sumaCantidadBajas);

            $trasplanteBolsa = Trasplante::select('tp_fecha')
                ->where('tp_pm_id', $tp_pm_id)
                ->where('tp_tipo','campo')
                ->where('tp_estado',1)
                ->first();

            // Tiene trasplante a bolsa
            if($trasplanteBolsa){
                $data['tp_fecha'] = substr($trasplanteBolsa->tp_fecha,0,10) ;
            }else{
                $data['tp_fecha'] = "";
            }
        }

        return response()->json([
            'data' => $data,
        ], 200);
    }

    /**
     * Método que guarda trasplante a campo si no existe, de lo contrario se actualiza, fecha de trasplante.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function storeTrasplanteCampo(TrasplanteCampoStoreRequest $request){

        $plantaMadre = PlantaMadre::select([
                'pm_id',
            ])
            ->where('pm_pro_id_lote', $request->id_lote)
            ->where('pm_estado', 1)
            ->first();

        if ($plantaMadre) {

            $trasplanteCampo = Trasplante::select(['tp_id'])
                ->where('tp_pm_id', $plantaMadre->pm_id)
                ->where('tp_tipo', 'campo')
                ->where('tp_estado', 1);

            // Consultando Trasplante a bolsa para crear registro de trasplante a campo con los mismos registros
            // de 'tp_tipo_lote','tp_ubicacion', 'tp_cantidad_area'
            $trasplanteBolsa = Trasplante::select([
                    'tp_tipo_lote',
                    'tp_ubicacion',
                    'tp_cantidad_area'
                ])
                ->where('tp_pm_id', $plantaMadre->pm_id)
                ->where('tp_tipo', 'bolsa')
                ->where('tp_estado', 1)
                ->first();

            // Si no existe trasplante a campo se crea
            if (count($trasplanteCampo->get()) == 0) {

                if (!$this->fnVerificaPermisoUsuario('CREAR')) {
                    throw new AuthorizationException;
                }

                $trasplanteCampo->create([
                    'tp_pm_id'          => $plantaMadre->pm_id,
                    'tp_tipo'           => 'campo',
                    'tp_tipo_lote'      => $trasplanteBolsa->tp_tipo_lote,
                    'tp_fecha'          => $request->tp_fecha." ".date('H:i:s'),
                    'tp_ubicacion'      => $trasplanteBolsa->tp_ubicacion,
                    'tp_cantidad_area'  => $trasplanteBolsa->tp_cantidad_area,
                    'tp_estado'         => true
                ]);
            }else{

                if (!$this->fnVerificaPermisoUsuario('EDITAR')) {
                    throw new AuthorizationException;
                }

                // Se actualiza trasplante a campo.
                $trasplanteCampo->update([
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

    public function deleteTrasplanteCampo(Request $request){
        if(!$request->filled('tp_id')){
            return response()->json([
                'message' => 'Error de Validación de Datos.',
                'errors'  => "El Id del trasplante campo del lote es requerido."
            ], 409);
        }

        $trasplanteCampo = Trasplante::where('tp_id',$request->tp_id)
            ->where('tp_tipo', 'campo')
            ->where('tp_estado', 1)
            ->first();

        if (!$trasplanteCampo) {
            return response()->json([
                'message' => "Error de validación de datos.",
                'errors' => "No se encontro la trasplante a campo con Id $request->tp_id",
            ], 404);
        }

        // CONSULTANDO SI EL LOTE TIENE COSECHA
        $cosechaLote = Cosecha::select('cos_id')
            ->where('cos_tp_id', $trasplanteCampo->tp_id)
            ->where('cos_estado', 1)
            ->first();
        if ($cosechaLote) {
            return response()->json([
                'message' => 'Error de Validación de Datos.',
                'errors'  => "No se puede eliminar trasplante a campo porque ya tiene cosecha."
            ], 409);
        }

        try {
            $trasplanteCampo->update([
                "tp_estado" => 0
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Acción inesperada',
                'errors'  => "Error al eliminar trasplante a campo.".$e
            ], 500);
        }

        return response()->json([
            'message' => "Se elimino el trasplante a campo con éxito.",
        ], 200);

    }
}
