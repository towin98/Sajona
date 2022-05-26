<?php

namespace App\Http\Controllers\Cosecha;

use Exception;
use App\Models\Cosecha;
use App\Models\Trasplante;
use App\Traits\bajasTrait;
use Illuminate\Http\Request;
use App\Traits\commonsTrait;
use App\Traits\paginationTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\cosechaRequest;
use App\Http\Requests\cosechaDeleteRequest;
use App\Http\Resources\listarCosechaCollection;
use App\Models\PostCosecha;
use Illuminate\Auth\Access\AuthorizationException;

class CosechaController extends Controller
{
    use paginationTrait;
    use bajasTrait;
    use commonsTrait;

    public function __construct()
    {
        $this->middleware(['permission:LISTAR'])->only('buscarCosechas');
        $this->middleware(['permission:CREAR|EDITAR'])->only('storeCosecha');
        $this->middleware(['permission:VER'])->only('showCosecha');
        $this->middleware(['permission:ELIMINAR'])->only('deleteCosecha');
    }

    /**
     * Se crea cosecha si no existe, de lo contrario se actualiza.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function storeCosecha(cosechaRequest $request)
    {

        $trasplanteCampo = Trasplante::select(['tp_id', 'tp_fecha'])
            ->where('tp_id', $request->tp_id)
            ->where('tp_tipo', 'campo')
            ->first();

        if ($trasplanteCampo) {

            $cosecha = Cosecha::select(['cos_id'])
                ->where('cos_estado', 1)
                ->where('cos_tp_id', $request->tp_id);

            // Dias transcurridos desde la fecha de trasplante campo hasta la fecha de cosecha.
            $fechaCosecha = date_create($request->cos_fecha_cosecha ." ".date('H:i:s'));
            $diasTranscurridosFloracion = date_diff(date_create($trasplanteCampo->tp_fecha),$fechaCosecha)->format('%a');

            try {

                if (!$this->fnVerificaPermisoUsuario('CREAR')) {
                    throw new AuthorizationException;
                }

                // Si no existe se crea
                if (count($cosecha->get()) == 0) {
                    $cosecha->create([
                        "cos_tp_id"             => $request->tp_id,
                        "cos_fecha_suelo"       => $trasplanteCampo->tp_fecha,
                        "cos_fecha_cosecha"     => $request->cos_fecha_cosecha ." ".date('H:i:s'),
                        "cos_numero_plantas"    => $request->cos_numero_plantas,
                        "cos_estado_cosecha"    => $request->cos_estado_cosecha,
                        "cos_dias_floracion"    => $diasTranscurridosFloracion,
                        "cos_peso_verde"        => $request->cos_peso_verde,
                        "cos_observacion"       => $request->cos_observacion,
                        "cos_estado"            => true
                    ]);
                }else{

                    if (!$this->fnVerificaPermisoUsuario('EDITAR')) {
                        throw new AuthorizationException;
                    }

                    // Se actualiza.
                    $cosecha->update([
                        "cos_tp_id"             => $request->tp_id,
                        "cos_fecha_suelo"       => $request->tp_fecha,
                        "cos_fecha_cosecha"     => $request->cos_fecha_cosecha ." ".date('H:i:s'),
                        "cos_numero_plantas"    => $request->cos_numero_plantas,
                        "cos_estado_cosecha"    => $request->cos_estado_cosecha,
                        "cos_dias_floracion"    => $diasTranscurridosFloracion,
                        "cos_peso_verde"        => $request->cos_peso_verde,
                        "cos_observacion"       => $request->cos_observacion
                    ]);
                }
            }catch (Exception $e) {
                return response()->json([
                    'message' => "Error Inesperado.",
                    'errors' => "El sistema no pudo realizar la acción solicitada.",
                ], 500);
            }

        }else{
            return response()->json([
                'errors' => "Para poder crear cosecha debe primero haber tenido trasplante a Campo el lote, Verifique.",
                'code' => 422
            ], 409);
        }

        return response()->json([
            'message' => 'Datos Guardados.',
        ], 201);
    }

    /**
     * Método que listar todas las cosechas.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function buscarCosechas(Request $request)
    {

        // Se válida si envian los parámetros length y start.
        if($request->has(['length', 'start'])){
            $length = $request->length;
            $start  = $request->start;
        }else{
            $length = 15;
            $start  = 0;
        }

        // Se buscan todos los trasplantes a bolsa.
        $registros = Trasplante::select(['tp_id', 'tp_pm_id', 'tp_fecha'])
            ->where('tp_tipo', 'campo')
            ->with([
                'getPlantaMadre' => function ($query){
                    $query->select([
                        'pm_id',
                        'pm_pro_id_lote'
                    ])
                    ->with([
                        'getPropagacion' => function ($query){
                            $query->select([
                                'pro_id_lote',
                                'pro_cantidad_plantas_madres'
                            ]);
                        }
                    ]);
                }
            ])
            ->with([
                'getCosecha' => function ($query){
                    $query->select([
                        'cos_id',
                        'cos_tp_id',
                        'cos_fecha_cosecha',
                        'cos_estado'
                    ]);
                    $query->where('cos_estado',1); // Registros no eliminados
                }
            ])
            ->BuscarCosecha($request->buscar);

            // consulta para saber cuantos registros hay.
            $totalRegistros = $registros->count();

            $registros = $registros->skip($start)
            ->take($length)
            ->get()
            ->toArray();

        $registros = listarCosechaCollection::collection($registros);

        // Ordenamiento
        if ($request->filled('orderColumn') && $request->filled('order')) {
            switch ($request->orderColumn) {
                case 'id_lote':
                case 'pro_cantidad_plantas_madres':
                case 'tp_fecha':
                case 'cos_fecha_cosecha':
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
     * Retorna información de un registro de cosecha.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showCosecha($id_trasplante)
    {

        $cosecha = Trasplante::select(['tp_id','tp_pm_id','tp_fecha', 'tp_ubicacion'])
            ->where('tp_id', $id_trasplante)
            ->where('tp_tipo', 'campo')
            ->with([
                'getPlantaMadre' => function ($query){
                    $query->select([
                        'pm_id',
                        'pm_pro_id_lote',
                        'pm_cantidad_semillas',
                        'pm_cantidad_esquejes'
                    ]);
                }
            ])
            ->with([
                'getCosecha' => function ($query){
                    $query->select([
                        "cos_tp_id",
                        "cos_fecha_suelo",
                        "cos_fecha_cosecha",
                        "cos_numero_plantas",
                        "cos_estado_cosecha",
                        "cos_dias_floracion",
                        "cos_peso_verde",
                        "cos_observacion",
                        "cos_estado",
                    ]);
                    $query->where('cos_estado',1); // Registros no eliminados
                }
            ])
            ->get();

        $cosecha = $cosecha->map(function ($value, $key){

            $sumaCantidadBajas = $this->cantidadBajas(optional($value->getPlantaMadre)->pm_pro_id_lote, ['esquejes','campo', 'bolsa','cosecha']);
            $cos_numero_plantas = ((optional($value->getPlantaMadre)->pm_cantidad_semillas + optional($value->getPlantaMadre)->pm_cantidad_esquejes) - $sumaCantidadBajas);

            // Dias transcurridos desde la fecha de trasplante campo hasta la fecha actual.
            $today = date_create();
            $diasTranscurridos = date_diff(date_create($value->tp_fecha),$today)->format('%a');

            return [
                "cos_tp_id"          => $value->tp_id,
                "tp_fecha"           => substr($value->tp_fecha,0,10),
                "cos_fecha_cosecha"  => substr(optional($value->getCosecha)->cos_fecha_cosecha,0,10),
                "id_lote"            => optional($value->getPlantaMadre)->pm_pro_id_lote,
                "cos_numero_plantas" => $cos_numero_plantas,
                "cos_estado_cosecha" => optional($value->getCosecha)->cos_estado_cosecha,
                "cos_dias_floracion" => (optional($value->getCosecha)->cos_dias_floracion == null) ? $diasTranscurridos : $value->getCosecha->cos_dias_floracion, // fecha campo - fecha cosecha
                "tp_ubicacion"       => $value->tp_ubicacion,
                "cos_peso_verde"     => optional($value->getCosecha)->cos_peso_verde,
                "cos_observacion"    => optional($value->getCosecha)->cos_observacion
            ];
        });

        if (count($cosecha) == 0) {
            return response()->json([
                'errors' => [
                    "No existe registros con el Id[$id_trasplante] proporcionado."
                ],
            ], 404);
        }

        return response()->json([
            'data' => $cosecha[0],
        ], 200);
    }

    /**
     * Elimina un registro de cosecha (cambia a estado 0)
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteCosecha(cosechaDeleteRequest $request)
    {
        try {

            // Consultando para obtener id del lote.
            $trasplante = Trasplante::select(['tp_id','tp_pm_id'])
            ->where('tp_id', $request->tp_id)
            ->where('tp_tipo', 'campo')
            ->with([
                'getPlantaMadre' => function ($query){
                    $query->select([
                        'pm_id',
                        'pm_pro_id_lote',
                    ]);
                }
            ])
            ->first();

            $cosecha = Cosecha::select(['cos_id', 'cos_estado'])->where('cos_tp_id', $request->tp_id)->where('cos_estado', 1)->first();
            if (!$cosecha) {
                return response()->json([
                    'message' => "Error de validación de datos.",
                    'errors' => "No se encontro la cosecha.",
                ], 404);
            }else{
                $postCosecha = PostCosecha::select('pos_id')->where('post_cos_id', $cosecha->cos_id)->where('post_estado', 1)->first();
                if ($postCosecha) {
                    // si hay un post cosecha para la cosecha en que se intenta eliminar se retorna error.
                    return response()->json([
                        'message' => "Validación del sistema",
                        'errors' => "El lote ".$trasplante->getPlantaMadre->pm_pro_id_lote." ya tiene un proceso en el Módulo post cosecha.",
                    ], 409);
                }else{
                    // No se elimina, se coloca en estado 0.
                    $cosecha->update([
                        'cos_estado' => 0
                    ]);

                    return response()->json([
                        'message' => "Se elimino cosecha para el lote ".$trasplante->getPlantaMadre->pm_pro_id_lote.".",
                    ], 200);
                }
            }
        }catch (Exception $e) {
            return response()->json([
                'message' => "Error en el sistema.",
                'errors' => "Error al eliminar cosecha.",
            ], 500);
        }
    }
}
