<?php

namespace App\Http\Controllers\Propagacion;

use App\Http\Controllers\Controller;
use App\Http\Requests\PropationRequest;
use App\Models\Propagacion;
use Illuminate\Http\Request;

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
        // Se válida si envian los parámetros length
        if(!$request->filled('length')){
            $length = 10;
        }else{
            $length = $request->length;
        }

        if ($request->filled('orderColumn') && $request->filled('order')) {
            $totalRegistros = Propagacion::Buscar($request->buscar)
                ->orderBy($request->orderColumn, $request->order)
                ->paginate($length);
        }else{
            $totalRegistros = Propagacion::Buscar($request->buscar)
                ->paginate($length);
        }

        return response()->json($totalRegistros, 200);
    }

    /**
     * Busca ultimo id de lote en DB.
     *
     * @return \Illuminate\Http\Response
     */
    public function buscarUltimoIdLote($controller = false)
    {
        $pro_id_lote = Propagacion::select("pro_id_lote")
                        ->orderByDesc("pro_id_lote")
                        ->first();

        $id = 0;
        if ($pro_id_lote) {
            $id = $pro_id_lote->pro_id_lote + 1;
        }else{
            $id = 1;
        }

        // Si este método ha sido ejecutado desde el mismo controllador retornará valor.
        if ($controller == true) {
            return $id;
        }

        return response()->json([
            'idLote' => $id,
        ], 200);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PropationRequest $request)
    {
        $id = $this->buscarUltimoIdLote(true);
        Propagacion::create([
            "pro_id_lote"                   => $id,
            "pro_fecha"                     => $request->pro_fecha." ".date("H:i:s"),
            "pro_tipo_propagacion"          => $request->pro_tipo_propagacion,
            "pro_variedad"                  => $request->pro_variedad,
            "pro_tipo_incorporacion"        => $request->pro_tipo_incorporacion,
            "pro_cantidad_material"         => $request->pro_cantidad_material,
            "pro_cantidad_plantas_madres"   => $request->pro_cantidad_plantas_madres,
            "pro_estado"                    => true,
        ]);

        return response()->json([
            'message' => 'Datos Guardados',
        ], 201);

    }
}
