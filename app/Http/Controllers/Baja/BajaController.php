<?php

namespace App\Http\Controllers\Baja;

use Throwable;
use App\Models\Baja;
use App\Models\PlantaMadre;
use App\Models\Propagacion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BajaController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:LISTAR'])->only('buscarLotes');
        $this->middleware(['permission:CREAR|EDITAR'])->only('store');
        $this->middleware(['permission:EDITAR'])->only('update');
        $this->middleware(['permission:VER'])->only('show');
    }

    /**
     * Método que busca lotes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function buscarLotes(Request $request)
    {

        // Se válida si envian los parámetros length y start.
        if ($request->has(['length', 'start'])) {
            $length = $request->length;
            $start  = $request->start;
        } else {
            $length = 15;
            $start  = 0;
        }

        $data = [];
        $registros = Propagacion::select(['pro_id_lote']);

        // Ordenamineto solo para lote.
        if ($request->filled('orderColumn') && $request->filled('order')) {
            if ($request->orderColumn == "id_lote") {
                $registros = $registros->orderBy('pro_id_lote',$request->order);
            }
        }
        $registros = $registros->with([
                'getBaja' => function ($query) {
                    $query->select([
                        "bj_pro_id_lote",
                        "bj_fecha",
                        "bj_cantidad"
                    ]);
                }
            ])
            ->has('getPlantaMadre')
            ->with([
                'getPlantaMadre' => function ($query) {
                    $query->select([
                        'pm_id',
                        'pm_pro_id_lote',
                        'pm_cantidad_semillas',
                        'pm_cantidad_esquejes',
                        'pm_estado'
                    ]);
                }
            ]);

        // consulta para saber cuantos registros hay.
        $totalRegistros = $registros->count();

        $registros = $registros->skip($start)
            ->take($length)
            ->get()
            ->toArray();

        // Tratando data bajas
        foreach ($registros as $registro) {
            $nBajas = 0;
            $nCantidadEsquejesSemillas = 0;
            $getPlantaMadre = $registro['get_planta_madre'];

            if (!empty($registro['get_baja'])) {
                foreach ($registro['get_baja'] as $baja) {
                    $nBajas += $baja['bj_cantidad'];
                }
                $registro = $baja; // Agrupando, sobre escribiendo.
            } else {
                unset($registro['get_baja']);
                unset($registro['get_planta_madre']); // Se borra repetidos.
            }

            $registro['descartes'] = $nBajas;
            unset($registro['bj_cantidad']);

            // Tratando data planta madre
            if (!empty($getPlantaMadre)) {
                $nCantidadEsquejesSemillas = $getPlantaMadre['pm_cantidad_semillas'] + $getPlantaMadre['pm_cantidad_esquejes'];
            }

            $registro['cantidadSemillasEsquejes'] = $nCantidadEsquejesSemillas;
            $data[] = $registro;
        }

        $registros = collect($data);

        $registros = $registros->map(function ($value) {
            return [
                'id_lote'                        => isset($value['bj_pro_id_lote']) ? $value['bj_pro_id_lote'] : $value['pro_id_lote'], // Tratando lote para que solo quede con un nombre
                'bj_fecha'                       => isset($value['bj_fecha']) ? $value['bj_fecha'] : '',
                'descartes'                      => $value['descartes'],
                'cantidadSemillasEsquejes'       => $value['cantidadSemillasEsquejes'],
            ];
        });

        if ($request->filled('orderColumn') && $request->filled('order')) {
            $registros = $registros->sortBy([[$request->orderColumn, strtolower($request->order)]]);
        }

        $buscar = $request->buscar;
        $registros = $registros->filter(function ($value, $key) use ($buscar) {
            // Buscando data
            if (false !== stristr($value['id_lote'], $buscar)) {
                return true;
            }
            if (false !== stristr($value['bj_fecha'], $buscar)) {
                return true;
            }
            if (false !== stristr($value['descartes'], $buscar)) {
                return true;
            }
            if (false !== stristr($value['cantidadSemillasEsquejes'], $buscar)) {
                return true;
            }
        });

        return response()->json([
            'data'      => $registros->values(),
            'filtrados' => $registros->count(),
            'total'     => $totalRegistros,
        ], 200);
    }

    /**
     * Actualiza la bajas de una lote.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $bajas = array();
        $bajas = $request->bajas;
        $id_lote = $request->id_lote;

        $errores = [];
        $contadorErrores = 0;

        foreach ($bajas as $baja) {

            $validator = Validator::make($baja, Baja::$rules, Baja::$messages);
            if ($validator->fails()) {
                $errores[] = $validator->errors();
                $contadorErrores++;
            } else {
                $errores[] = json_decode("{}");
            }
        }

        if ($contadorErrores > 0) {
            return response()->json([
                'message' => 'Error de Validación de Datos',
                'errors' => $errores
            ], 422);
        }

        // Consultando en planta madre la cantidad de siembra o cantidad propagada.
        // Validando que la cantidad sembrada no sea menos a la descartes.
        $plantaMadre = PlantaMadre::select(['pm_id','pm_pro_id_lote', 'pm_cantidad_semillas', 'pm_cantidad_esquejes'])
            ->where('pm_pro_id_lote', $request->id_lote)
            ->first();

        if (!$plantaMadre) {
            return response()->json([
                'message' => 'Error de Validación de Datos',
                'errors' => "El lote $request->id_lote aun no tiene esquejaciones."
            ], 409);
        }else{
            $totalSemillasEsquejes = $plantaMadre->pm_cantidad_semillas + $plantaMadre->pm_cantidad_esquejes;
            $totalDescartes = 0;
            foreach ($bajas as $baja) {
                $totalDescartes += $baja['bj_cantidad'];
            }
            if ($totalDescartes > $totalSemillasEsquejes) {
                return response()->json([
                    'message' => 'Error de Validación de Datos',
                    'errors' => "El total de descartes ($totalDescartes) para el lote $request->id_lote no puede superar la cantidad siembra ($totalSemillasEsquejes)."
                ], 409);
            }
        }

        try {
            Baja::where('bj_pro_id_lote', $id_lote)->delete();
            foreach ($bajas as $baja) {
                $baja['bj_fecha']        = $baja['bj_fecha'] . " " . date('H:i:s');
                $baja['bj_estado']       = true;
                $baja['bj_pro_id_lote']  = $id_lote;
                Baja::create($baja);
            }
        } catch (Throwable $e) {
            return response()->json([
                'errors' => 'Error al guardar Bajas, comunicate con el area de Sistemas.',
            ], 500);
        }

        return response()->json([
            'message' => 'Datos Guardados.',
        ], 200);
    }

    /**
     * Consulta las bajas de un lote.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id_lote)
    {
        $registrosBajas = baja::where('bj_pro_id_lote', $id_lote)
            ->get()
            ->map(function ($baja) {
                return [
                    "bj_pro_id_lote"    => $baja->bj_pro_id_lote,
                    "bj_fecha"          => substr($baja->bj_fecha, 0, 10),
                    "bj_cantidad"       => $baja->bj_cantidad,
                    "bj_fase_cultivo"   => $baja->bj_fase_cultivo,
                    "bj_motivo_perdida" => $baja->bj_motivo_perdida,
                    "bj_observacion"    => $baja->bj_observacion
                ];
            });

        return response()->json([
            'data' => $registrosBajas,
        ], 200);
    }
}
