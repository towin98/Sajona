<?php

namespace App\Http\Controllers\Parametro;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\Parametricas\ParametricaTrait;

class ParametroController extends Controller
{
    use ParametricaTrait;

    /**
     * Método que busca registros, lista registros de una tabla parámetrica.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function buscar(Request $request){
        // Se válida si envian los parámetros length y start.
        if($request->has(['length', 'start'])){
            $length = $request->length;
            $start  = $request->start;
        }else{
            $length = 15;
            $start  = 0;
        }

        $modelo = $this->getModelo($request->parametrica);

        // Validación de los datos.
        $arrErrores = $this->validarDatos($request->parametrica, $modelo);

        // Si se presentaron errores se retornan.
        if (count($arrErrores) > 0) {
            return response()->json([
                'message' => 'Error de Validación de Datos',
                'errors'  => $arrErrores
            ], 409);
        }

        $registros = $modelo::Buscar($request->buscar)
            ->Ordenamiento($request->orderColumn, $request->order);

        $totalRegistros = $modelo::count();

        $registros = $registros->skip($start)
            ->take($length)
            ->get();

        return response()->json([
            'data'      => $registros,
            'filtrados' => $registros->count(),
            'total'     => $totalRegistros,
        ], 200);
    }

    /**
     * Método que guardar registros de una tabla parametro.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        try {
            $modelo  = $this->getModelo($request->parametrica);

            if($modelo == ''){
                $arrErrors[] = ($request->parametrica != '') ? 'No se encontró la paramétrica '. $request->parametrica : 'No se envió el parámetro de la paramétrica o se encuentra vacía';
                return response()->json([
                    'message' => 'Error de Validación de Datos',
                    'errors'  => $arrErrors
                ], 500);
            }

            // Validación de los datos.
            $arrErrores = $this->validarDatos($request->parametrica, $modelo, 'store', $request);

            // Si se presentaron errores se retornan.
            if (count($arrErrores) > 0) {
                return response()->json([
                    'message' => 'Error de Validación de Datos',
                    'errors'  => $arrErrores
                ], 422);
            }

            $registro = $modelo::where('descripcion', strtoupper($request->descripcion))->first();
            if ($registro) {
                return response()->json([
                    'message' => 'Error de Validación de Datos',
                    'errors' => [
                        "descripcion" => "La descripción ya existe."
                    ],
                ], 422);
            }

            $modelo::create([
                'descripcion' => strtoupper($request->descripcion),
                'estado'      => $request->estado,
            ]);

            $nombreCampoParametro  = $this->getCampoParametro($request->parametrica);

            return response()->json([
                'message' => "Datos Guardados, para el campo parámetro[$nombreCampoParametro].",
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error de Validación de Datos',
                'errors' => [
                    "Error al crear registros, por favor comuniquese con el area de Tecnología Sajona."
                ],
            ], 500);
        }
    }

    /**
     * Muestra data de un resultado en especifico de una parametrica.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($parametrica,$id)
    {
        $modelo  = $this->getModelo($parametrica);

        $arrErrores = $this->validarDatos($parametrica, $modelo);

        // Si se presentaron errores se retornan.
        if (count($arrErrores) > 0) {
            return response()->json([
                'message' => 'Error de Validación de Datos',
                'errors'  => $arrErrores
            ], 409);
        }

        $dataParametros = $modelo::findOrfail($id);

        return response()->json([
            'data'      => $dataParametros,
        ], 200);
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
        $modelo  = $this->getModelo($request->parametrica);

        $arrErrores = $this->validarDatos($request->parametrica, $modelo, 'update', $request);

        // Si se presentaron errores se retornan.
        if (count($arrErrores) > 0) {
            return response()->json([
                'message' => 'Error de Validación de Datos',
                'errors'  => $arrErrores
            ], 422);
        }

        try {
            $modelo::findOrfail($id)->update([
                'descripcion' => strtoupper($request->descripcion),
                'estado'      => $request->estado
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error en el sistema.',
                'errors' => [
                    "Error al actualizar registros, por favor comuniquese con el area de Tecnología Sajona."
                ],
            ], 500);
        }

        $nombreCampoParametro  = $this->getCampoParametro($request->parametrica);

        return response()->json([
            'message' => "Datos Actualizados para el campo parámetro[$nombreCampoParametro].",
        ], 201);
    }

    /**
     * Método para validar modelo y campos enviados.
     *
     * @param  string $parametrica Nombre de la paramétrica
     * @param  string $modelo Modelo de la paramétrica
     * @param  string $accion Acción para definir las reglas de validación del modelo
     * @param  \Illuminate\Http\Request $request Request recibido
     * @return array $arrErrors Errores de las validaciones
     */
    public function validarDatos($parametrica, $modelo, $accion = '', $request = null) {
        $arrErrors = [];

        if($modelo == ''){
            $arrErrors[] = ($parametrica != '') ? 'No se encontró la paramétrica '. $parametrica : 'No se envió el parámetro de la paramétrica o se encuentra vacía';
            return $arrErrors;
        }

        if ($request != null && $accion != '') {
            // Validación conforme a reglas del modelo.
            $objValidator = Validator::make($request->all(), ParametricaTrait::$rules, ParametricaTrait::$messages);
            if($objValidator->fails()){
                $arrErrors = $objValidator->messages();
            }

            $model = new $modelo;
            $arrFillable = array_diff(array_keys($request->except(['parametrica'])), $model->getFillable());

            if(count($arrFillable) > 0 ){
                $atributos = "";
                foreach ($arrFillable as $fillable) {
                    $atributos .= $fillable.",";
                }
                $arrErrors[] = "Los siguientes campos no son aceptados: ".substr($atributos,0,-1);
            }
        }

        return $arrErrors;
    }
}
