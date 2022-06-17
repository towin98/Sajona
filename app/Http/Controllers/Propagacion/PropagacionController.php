<?php

namespace App\Http\Controllers\Propagacion;

use Exception;
use App\Models\Propagacion;
use App\Models\PlantaMadre;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PropationRequest;
use App\Http\Requests\PropationRequestUpdate;

class PropagacionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:LISTAR'])->only('listar');
        $this->middleware(['permission:CREAR'])->only('store');
        $this->middleware(['permission:EDITAR'])->only('update');
        $this->middleware(['permission:ELIMINAR'])->only('delete');
        $this->middleware(['permission:VER'])->only('show');
    }

    /**
     * Muestra listado de propagaciones.
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
                ->where('pro_estado',1)
                ->with('getTipoPropagacion:id,nombre')
                ->with('getTipoIncorporacion:id,nombre')
                ->paginate($length);
        }else{
            $totalRegistros = Propagacion::Buscar($request->buscar)
                ->where('pro_estado',1)
                ->with('getTipoPropagacion:id,nombre')
                ->with('getTipoIncorporacion:id,nombre')
                ->paginate($length);
        }

        $totalRegistros->getCollection()->transform(function($data, $key) {
            return [
                "pro_id_lote"                   => $data->pro_id_lote,
                "pro_fecha"                     => $data->pro_fecha,
                "pro_tipo_propagacion"          => $data->getTipoPropagacion->nombre,
                "pro_variedad"                  => $data->pro_variedad,
                "pro_tipo_incorporacion"        => $data->getTipoIncorporacion->nombre,
                "pro_cantidad_material"         => $data->pro_cantidad_material,
                "pro_cantidad_plantas_madres"   => $data->pro_cantidad_plantas_madres,
                "pro_estado"                    => $data->pro_estado,
            ];
        });

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

    /**
     * Muestra informacion de una propagación creada.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $propagacion = Propagacion::findOrfail($id);

        $propagacion->pro_fecha = substr($propagacion->pro_fecha,0,10);
        return response()->json([
            'data'      => $propagacion,
        ], 200);
    }

    /**
     * Actualiza propagación si esta no tiene aun procesos en los demás módulos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id de propagación
     * @return \Illuminate\Http\Response
     */
    public function update(PropationRequestUpdate $request, $id)
    {
        $propagacion = Propagacion::findOrfail($id);

        if ($propagacion) {
            $plantaMadre = PlantaMadre::select('pm_id')->where('pm_pro_id_lote', $propagacion->pro_id_lote)->first();
            if ($plantaMadre) {
                return response()->json([
                    'message' => 'Error de Validación',
                    'errors'  => "El lote[$propagacion->pro_id_lote] ya tiene procesos y no se puede editar."
                ], 409);
            }else{
                try {
                    $request->merge(['pro_fecha' => $request->pro_fecha." ".date("H:i:s")]);
                    $propagacion->update($request->all());
                } catch (\Exception $e) {
                    return response()->json([
                        'message' => 'Error en el Sistema',
                        'errors'  => "Error al actualizar propagación, por favor comuniquese con el area de Tecnología de Sajona, Gracias"
                    ], 500);
                }
            }
        }

        return response()->json([
            'message' => "Datos Actualizados con exito.",
        ], 201);

    }

    /**
     * Elimina un registro de cosecha (cambia a estado 0).
     * El eliminado en propagación funciona cambiando de estado los registros a Estado 0.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        if(!$request->filled('pro_id_lote')){
            return response()->json([
                'message' => 'Error de Validación de Datos.',
                'errors'  => "El Id del Lote es requerido."
            ], 409);
        }

        $propagacion = Propagacion::where('pro_id_lote',$request->pro_id_lote)->where('pro_estado', 1)->first();
        if (!$propagacion) {
            return response()->json([
                'message' => "Error de validación de datos.",
                'errors' => "No se encontro la Propagación con Id de Lote[$request->pro_id_lote]",
            ], 404);
        }

        $plantaMadre = PlantaMadre::select('pm_id')->where('pm_pro_id_lote', $propagacion->pro_id_lote)->first();
        if ($plantaMadre) {
            return response()->json([
                'message' => 'Error de Validación',
                'errors'  => "El lote $propagacion->pro_id_lote ya tiene procesos y NO se puede eliminar."
            ], 409);
        }

        try {
            $propagacion = $propagacion->update([
                'pro_estado' => 0
            ]);
            return response()->json([
                'message' => "Se elimino el lote ".$request->pro_id_lote." correctamente.",
            ], 200);

        }catch (Exception $e) {
            return response()->json([
                'message' => "Error en el sistema Inesperado.",
                'errors' => "Error al eliminar Propagación.".$e,
            ], 500);
        }
    }
}
