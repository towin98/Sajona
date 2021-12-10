<?php

namespace App\Http\Controllers\Propagacion;

use App\Http\Controllers\Controller;
use App\Models\Propagacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PropagacionController extends Controller
{
    public function __construct()
    {
        // $this->middleware('role:Auxiliar');
        // $this->middleware(['role:Auxiliar','permission:publish articles|edit articles']);
    }


    /**
     * Muestra una lista del recurso.
     *
     * @return \Illuminate\Http\Response
     */
    public function listar(Request $request)
    {
        $totalRegistros = Propagacion::count();

        // Se válida si envian los parámetros length y start.
        if($request->has(['length', 'start'])){
            $length = ($request->length != -1) ? $request->length : $totalRegistros;
            $start  = ($request->length != -1) ? $request->start : 0;
        }else{
            $length = 10;
            $start  = 0;
        }

        $registros = Propagacion::select("*")
            ->skip($start)
            ->take($length)
            ->get();

        return response()->json([
            'data'      => $registros,
            'filtrados' => $registros->count(),
            'total'     => $totalRegistros,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $reglas =  [
            'pro_fecha'                     => 'required|date',
            'pro_tipo_propagacion'          => 'required|string',
            'pro_variedad'                  => 'required|numeric',
            'pro_tipo_incorporacion'        => 'required|string',
            'pro_cantidad_material'         => 'required|numeric',
            'pro_cantidad_plantas_madres'   => 'required|numeric',
        ];

        $objValidator = Validator::make($request->all(),  $reglas, Propagacion::$messagesValidators);
        if($objValidator->fails()){
            return response()->json([
                'message' => 'Error de Validación de Datos',
                'errors'  => $objValidator->errors()
            ], 422);
        }

        $pro_id_lote = Propagacion::select("pro_id_lote")
                        ->orderByDesc("pro_id_lote")
                        ->first();

        $id = 0;
        if ($pro_id_lote) {
            $id = $pro_id_lote->pro_id_lote + 1;
        }else{
            $id = 1;
        }

        Propagacion::create([
            "pro_id_lote"                   => $id,
            "pro_fecha"                     => $request->pro_fecha,
            "pro_tipo_propagacion"          => $request->pro_tipo_propagacion,
            "pro_variedad"                  => $request->pro_variedad,
            "pro_tipo_incorporacion"        => $request->pro_tipo_incorporacion,
            "pro_cantidad_material"         => $request->pro_cantidad_material,
            "pro_cantidad_plantas_madres"   => $request->pro_cantidad_plantas_madres,
            "pro_estado"                    => true,
        ]);

        return response()->json([
            'message' => 'Datos Guardados',
        ], 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
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
