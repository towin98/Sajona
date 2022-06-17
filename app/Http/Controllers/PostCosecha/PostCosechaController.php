<?php

namespace App\Http\Controllers\PostCosecha;

use Exception;
use App\Models\Cosecha;
use App\Models\PostCosecha;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostCosechasDeleteRequest;
use App\Http\Requests\PostCosechasStoreRequest;
use App\Http\Resources\ListarPostCosechaCollection;
use App\Traits\commonsTrait;
use Illuminate\Auth\Access\AuthorizationException;

class PostCosechaController extends Controller
{
    use commonsTrait;

    public function __construct()
    {
        $this->middleware(['permission:LISTAR'])->only('buscarPostCosechas');
        $this->middleware(['permission:CREAR|EDITAR'])->only('storePostCosecha');
        $this->middleware(['permission:ELIMINAR'])->only('deletePostCosecha');
        $this->middleware(['permission:VER'])->only('showPosCosecha');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function buscarPostCosechas(Request $request)
    {

        // Se válida si envian los parámetros length y start.
        if($request->has(['length', 'start'])){
            $length = $request->length;
            $start  = $request->start;
        }else{
            $length = 15;
            $start  = 0;
        }

        // Buscando lotes que ya tengan cosecha.
        $registrosPostCosecha = Cosecha::select(['cos_id', 'cos_tp_id'])
            ->where('cos_estado', 1)
            ->with([
                'getTrasplanteCampo' => function ($query){
                    $query->select([
                        'tp_id',
                        'tp_pm_id'
                    ])
                    ->with([
                        'getPlantaMadre' => function ($query){
                            $query->select([
                                'pm_id',
                                'pm_pro_id_lote'
                            ]);
                        }
                    ]);
                }
            ])
            ->whereHas('getTrasplanteCampo', function ($query) {
                $query->where('tp_tipo', 'campo');
            })
            ->with([
                'getPostCosecha' => function ($query){
                    $query->select([
                        'pos_id',
                        'post_cos_id',
                        'post_fecha_ini_secado',
                        'post_fecha_fin_secado'
                    ]);
                    $query->where('post_estado', 1);
                }
            ])
            ->BuscarPostCosecha($request->buscar);

        // consulta para saber cuantos registros hay.
        $totalRegistros = $registrosPostCosecha->count();

        $registrosPostCosecha = $registrosPostCosecha->skip($start)
        ->take($length)
        ->get()
        ->toArray();

        $registrosPostCosecha = ListarPostCosechaCollection::collection($registrosPostCosecha);

        // Ordenamiento
        if ($request->filled('orderColumn') && $request->filled('order')) {
            switch ($request->orderColumn) {
                case 'id_lote':
                case 'post_fecha_ini_secado':
                case 'post_fecha_fin_secado':
                case 'estado':
                    $registrosPostCosecha = collect($registrosPostCosecha)->sortBy([
                        [$request->orderColumn, strtolower($request->order)]
                    ]);
                break;
            }
        }

        return response()->json([
            'data'      => $registrosPostCosecha,
            'filtrados' => $registrosPostCosecha->count(),
            'total'     => $totalRegistros,
        ], 200);
    }

    /**
     * Guarda o actualiza post cosecha
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storePostCosecha(PostCosechasStoreRequest $request)
    {

        // Verificando que exista una cosecha para el lote.
        $cosecha = Cosecha::select('cos_id')
            ->where('cos_id', $request->cos_id)
            ->where('cos_estado', 1)
            ->first();
        if (!$cosecha) {
            return response()->json([
                'errors' => "No existe una cosecha con el Id[$request->cos_id] digitado, debe tener una una cosecha para poder crear post cosecha.",
            ], 409);
        }else{
            // Consultar si existe post cosecha para actualizar o crear.
            $postCosecha = PostCosecha::select('pos_id')
                ->where('post_cos_id',$request->cos_id)
                ->where('post_estado', 1);

            // Biomasa  =  peso verde campo - peso flor verde.
            $biomasa = ($request->cos_peso_verde - $request->post_peso_flor_verde);

            if (count($postCosecha->get()) == 0) {

                if (!$this->fnVerificaPermisoUsuario('CREAR')) {
                    throw new AuthorizationException;
                }

                $accion = "Guardados";
                // Si no existe se crea
                $postCosecha->create([
                    "post_cos_id"               => $request->cos_id,
                    "post_fecha_ini_secado"     => $request->post_fecha_ini_secado." ".date('H:i:s'),
                    "post_fecha_fin_secado"     => $request->post_fecha_fin_secado." ".date('H:i:s'),
                    "post_peso_flor_verde"      => $request->post_peso_flor_verde,
                    "post_peso_biomasa"         => $biomasa,
                    "post_peso_flor_seco"       => $request->post_peso_flor_seco,
                    "post_porcentaje_humedad"   => $request->post_porcentaje_humedad,
                    "post_cbd"                  => $request->post_cbd,
                    "post_thc"                  => $request->post_thc,
                    "post_observacion"          => strtoupper($request->post_observacion),
                    "post_estado"               => 1,
                ]);

            }else{

                if (!$this->fnVerificaPermisoUsuario('EDITAR')) {
                    throw new AuthorizationException;
                }

                // Se actualiza.
                $accion = "Actualizados";
                $postCosecha->update([
                    "post_cos_id"               => $request->cos_id,
                    "post_fecha_ini_secado"     => $request->post_fecha_ini_secado." ".date('H:i:s'),
                    "post_fecha_fin_secado"     => $request->post_fecha_fin_secado." ".date('H:i:s'),
                    "post_peso_flor_verde"      => $request->post_peso_flor_verde,
                    "post_peso_biomasa"         => $biomasa,
                    "post_peso_flor_seco"       => $request->post_peso_flor_seco,
                    "post_porcentaje_humedad"   => $request->post_porcentaje_humedad,
                    "post_cbd"                  => $request->post_cbd,
                    "post_thc"                  => $request->post_thc,
                    "post_observacion"          => strtoupper($request->post_observacion),
                    "post_estado"               => 1,
                ]);
            }
        }
        return response()->json([
            'message' => "Datos $accion.",
        ], 201);
    }

    /**
     * Muestra una post cosecha.
     *
     * @param  int  $id_cosecha Id se cosecha.
     * @return \Illuminate\Http\Response
     */
    public function showPosCosecha($id_cosecha)
    {
        $registro = Cosecha::select([
                'cos_id'
            ])
            ->where('cos_id', $id_cosecha)
            ->where('cos_estado', 1) // Registro no eliminados
            ->first();

        if (!$registro) {
            return response()->json([
                'errors' => [
                    "No existen registros."
                ],
            ], 404);
        }

        $registroPostCosecha = Cosecha::select([
                        'cos_id',
                        'cos_tp_id',
                        'cos_fecha_cosecha',
                        'cos_numero_plantas',
                        'cos_estado_cosecha',
                        'cos_dias_floracion',
                        'cos_peso_verde',
                    ])
            ->where('cos_id', $id_cosecha)
            ->where('cos_estado', 1) // Registro no eliminados
            ->with([
                'getTrasplanteCampo' => function ($query){
                    $query->select([
                        'tp_id',
                        'tp_pm_id'
                    ])
                    ->with([
                        'getPlantaMadre' => function ($query){
                            $query->select([
                                'pm_id',
                                'pm_pro_id_lote'
                            ]);
                        }
                    ]);
                }
            ])
            ->with([
                'getPostCosecha' => function ($query){
                    $query->select([
                        'pos_id',
                        'post_cos_id',
                        'post_fecha_ini_secado',
                        'post_fecha_fin_secado',
                        'post_peso_flor_verde',
                        'post_peso_flor_seco',
                        'post_porcentaje_humedad',
                        'post_cbd',
                        'post_thc',
                        'post_observacion'
                    ]);
                    $query->where('post_estado', 1);
                }
            ])
            ->first();

            $registroPostCosecha = collect([$registroPostCosecha]);

            $registroPostCosecha = $registroPostCosecha->map(function ($value, $key) {

                // Biomasa  =  peso verde campo - peso flor verde.
                $biomasa = ($value->cos_peso_verde - $value->getPostCosecha?->post_peso_flor_verde);

                if ($value->getPostCosecha?->post_peso_flor_verde > 0) {
                    // Porcentaje humedad = (Peso flor verde / peso flor seco)*100 %.
                    $porcentajeRendimiento = round(($value->getPostCosecha?->post_peso_flor_seco / $value->getPostCosecha?->post_peso_flor_verde)*100,2);
                }else{
                    $porcentajeRendimiento = 0;
                }
                return [
                    'id_lote'                       => $value->getTrasplanteCampo?->getPlantaMadre?->pm_pro_id_lote,
                    'cos_id'                        => $value->cos_id,
                    'cos_numero_plantas'            => $value->cos_numero_plantas,
                    'cos_estado_cosecha'            => $value->cos_estado_cosecha,
                    'cos_fecha_cosecha'             => substr($value->cos_fecha_cosecha,0,10),
                    'cos_dias_floracion'            => $value->cos_dias_floracion,
                    'cos_peso_verde'                => $value->cos_peso_verde,
                    'post_fecha_ini_secado'         => substr($value->getPostCosecha?->post_fecha_ini_secado,0,10),
                    'post_fecha_fin_secado'         => substr($value->getPostCosecha?->post_fecha_fin_secado,0,10),
                    'post_peso_flor_verde'          => $value->getPostCosecha?->post_peso_flor_verde,
                    'post_peso_biomasa'             => $biomasa,
                    'post_peso_flor_seco'           => $value->getPostCosecha?->post_peso_flor_seco,
                    'post_porcentaje_humedad'       => $value->getPostCosecha?->post_porcentaje_humedad,
                    'post_cbd'                      => $value->getPostCosecha?->post_cbd,
                    'post_thc'                      => $value->getPostCosecha?->post_thc,
                    'post_porcentaje_rendimiento'   => $porcentajeRendimiento,
                    'post_observacion'              => $value->getPostCosecha?->post_observacion
                ];
            });

            return response()->json([
                'data' => $registroPostCosecha[0],
            ], 200);
    }

    /**
     * Elimina un registro de post cosecha (cambia a estado 0)
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deletePostCosecha(PostCosechasDeleteRequest $request) {
        try {

            $id_cosecha = PostCosecha::select('post_cos_id')
                ->where('pos_id', $request->pos_id)
                ->where('post_estado', 1)
                ->first();

            // No se elimina, se coloca en estado 0.
            $postCosecha = PostCosecha::where('pos_id', $request->pos_id)
                ->where('post_estado', 1)
                ->update([
                    'post_estado' => 0
                ]);

            if (!$postCosecha) {
                return response()->json([
                    'message' => "Error de validación de datos.",
                    'errors' => "No se encontro la post cosecha.",
                ], 404);
            }else{

                // Consultando para obtener id del lote.
                $id_lote = Cosecha::select(['cos_id', 'cos_tp_id'])
                ->where('cos_estado', 1)
                ->where('cos_id', $id_cosecha->post_cos_id)
                ->with([
                    'getTrasplanteCampo' => function ($query){
                        $query->select([
                            'tp_id',
                            'tp_pm_id'
                        ])
                        ->with([
                            'getPlantaMadre' => function ($query){
                                $query->select([
                                    'pm_id',
                                    'pm_pro_id_lote'
                                ]);
                            }
                        ]);
                    }
                ])
                ->first();

                return response()->json([
                    'message' => "Se elimino la Post Cosecha para el lote ".$id_lote->getTrasplanteCampo->getPlantaMadre->pm_pro_id_lote.".",
                ], 200);
            }

        }catch (Exception $e) {
            return response()->json([
                'message' => "Error en el sistema.",
                'errors' => "Error al eliminar post cosecha.",
            ], 500);
        }
    }
}
