<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Alerta;
use Illuminate\Http\Request;
use App\Http\Requests\AlertaRequest;

class AlertaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:CREAR'])->only('store');
        $this->middleware(['permission:EDITAR'])->only('update');
        $this->middleware(['permission:VER|LISTAR'])->only('show');
    }

    /**
     * Método que crea alerta en el sistema si no hay una creada.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AlertaRequest $request)
    {
        $alerta = Alerta::first();
        if (!$alerta) {
            try {
                Alerta::create([
                    // "min_rang_propagacion"  => $request->min_rang_propagacion,
                    "max_rang_propagacion"  => $request->max_rang_propagacion,
                    // "min_rang_bolsa"        => $request->min_rang_bolsa,
                    "max_rang_bolsa"        => $request->max_rang_bolsa,
                    // "min_rang_campo"        => $request->min_rang_campo,
                    "max_rang_campo"        => $request->max_rang_campo,
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'message' => "Error Inesperado.",
                    'errors'  => "Error al guardar alerta.",
                ], 500);
            }

            return response()->json([
                'message' => 'Alerta Guardada',
            ], 201);
        }else{
            return response()->json([
                'message' => "Error de Validación de Datos",
                'errors'  => "Ya existe una alerta creada en el Sistema.",
            ], 409);
        }
    }

    /**
     * Método retorna data de alerta.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $alerta = Alerta::first();
        if (!$alerta) {
            $alerta = [];
        }
        return response()->json([
            'data'      => $alerta,
        ], 200);
    }

    /**
     * Método que actualiza alerta.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id Id de alerta para actualizar.
     * @return \Illuminate\Http\Response
     */
    public function update(AlertaRequest $request, $id)
    {
        $alerta = Alerta::findOrfail($id);
        try {
            $alerta->update([
                // "min_rang_propagacion"  => $request->min_rang_propagacion,
                "max_rang_propagacion"  => $request->max_rang_propagacion,
                // "min_rang_bolsa"        => $request->min_rang_bolsa,
                "max_rang_bolsa"        => $request->max_rang_bolsa,
                // "min_rang_campo"        => $request->min_rang_campo,
                "max_rang_campo"        => $request->max_rang_campo,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => "Error Inesperado.",
                'errors'  => "Error al Actualizar alerta, por favor coloquese en contacto con el área de tecnología Sajona.",
            ], 500);
        }

        return response()->json([
            'message' => 'Se actualizo la Alerta.',
        ], 201);
    }
}
