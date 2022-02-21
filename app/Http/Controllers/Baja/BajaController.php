<?php

namespace App\Http\Controllers\Baja;

use App\Http\Controllers\Controller;
use App\Models\Baja;
use App\Models\Propagacion;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Optional;
use Throwable;
use Illuminate\Support\Facades\Validator;

class BajaController extends Controller
{
    /**
     * Método que busca lotes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function buscarLotes(Request $request)
    {

        // Se válida si envian los parámetros length
        if (!$request->filled('length')) {
            $length = 15;
        } else {
            $length = $request->length;
        }

        $data = [];
        $registros = Propagacion::select(['pro_id_lote'])
            ->with([
                'getBaja' => function ($query) {
                    $query->select([
                        'bj_pro_id_lote',
                        'bj_fecha',
                        'bj_cantidad'
                    ]);
                }
            ])
            ->with('getPlantaMadre')
            ->get()
            ->where('getPlantaMadre','!=',null)
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
                    $registro = $baja; // Agrupando
                }else{
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
            $registros = $registros->sortBy([[$request->orderColumn, $request->order]]);
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

        $registros = $registros->toArray();

        $pageName = 'page';
        $currentPage = $request->page;
        if ($currentPage == null) {
            $currentPage = 1;
        }
        $currentElements = array_slice($registros, $length * ($currentPage - 1), $length);

        $page = Paginator::resolveCurrentPage($pageName);
        $registros =  new LengthAwarePaginator($currentElements, count($registros), $length, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);

        return response()->json($registros, 200);
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
            }else{
                $errores[] = json_decode("{}");
            }
        }

        if ($contadorErrores > 0) {
            return response()->json([
                'message' => 'Error de Validación de Datos',
                'errores' => $errores
            ], 422);
        }

        try {
            Baja::where('bj_pro_id_lote', $id_lote)->delete();
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Error al guardar Bajas, comunicate con el area de Sistemas.',
            ], 500);
        }

        foreach ($bajas as $baja) {
            $baja['bj_estado']       = true;
            $baja['bj_pro_id_lote']  = $id_lote;
            Baja::create($baja);
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
                    "bj_pro_id_lote"  => $baja->bj_pro_id_lote,
                    "bj_fecha"        => substr($baja->bj_fecha,0,10),
                    "bj_cantidad"     => $baja->bj_cantidad,
                    "bj_fase_cultivo" => $baja->bj_fase_cultivo,
                    "bj_observacion"  => $baja->bj_observacion
                ];
            });

        return response()->json([
            'data' => $registrosBajas,
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
