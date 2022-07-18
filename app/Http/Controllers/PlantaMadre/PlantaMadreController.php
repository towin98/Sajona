<?php

namespace App\Http\Controllers\PlantaMadre;

use Throwable;
use Illuminate\Auth\Access\AuthorizationException;

use App\Models\PlantaMadre;
use App\Models\Propagacion;
use App\Traits\alertaTrait;
use App\Traits\commonsTrait;
use App\Traits\bajasTrait;
use App\Models\Trasplante;
use App\Http\Controllers\Controller;
use App\Http\Requests\PlantaMadreBuscarRequest;
use App\Http\Requests\PlantaMadreStoreRequest;

class PlantaMadreController extends Controller
{
    use alertaTrait;
    use commonsTrait;
    use bajasTrait;

    public function __construct()
    {
        $this->middleware(['permission:LISTAR'])->only('buscarLotes');
        $this->middleware(['permission:CREAR|EDITAR'])->only('store');
        $this->middleware(['permission:VER'])->only('show');
    }

    /**
     * Método que busca lotes de propagación por un rango de fechas.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function buscarLotes(PlantaMadreBuscarRequest $request)
    {
        // Se válida si envian los parámetros length
        if(!$request->filled('length')){
            $length = 10;
        }else{
            $length = $request->length;
        }

        $registros = Propagacion::select('*')
            ->with('getPlantaMadre')
            ->BuscarPLantaMadre($request->buscar);

        // Consultas
        if ($request->filled('orderColumn') && $request->filled('order')) {
            // Consulta Valor a buscar y ordenar registros ACS o DESC por una columna.
            switch ($request->orderColumn) {
                case 'pm_fecha_esquejacion':
                    $registros = $registros->leftJoin('planta_madre', 'planta_madre.pm_pro_id_lote', '=', 'propagacion.pro_id_lote')
                        ->orderBy('planta_madre.pm_fecha_esquejacion', $request->order);
                break;
                default:
                    $registros = $registros->orderBy($request->orderColumn, $request->order);
                break;
            }
        }

        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $registros = $registros->whereBetween('pro_fecha',[$request->fecha_inicio.' 00:00:00', $request->fecha_fin.' 23:59:59']);
        }

        $registros = $registros->paginate($length);

        // Requerido, consultando rango de trasplantes.
        $this->fnconsultarRangosAlerta();

        $registros->getCollection()->transform(function($data, $key) {

            $arrAlerta = $this->alerta($data);

            $evento            = $arrAlerta[0];
            $color             = $arrAlerta[1];
            $diasTranscurridos = $arrAlerta[2];

            return [
                'pro_id_lote'                   => $data->pro_id_lote,              // ( Id lote )
                'pro_fecha'                     => $data->pro_fecha,                // ( Fecha propagación )
                'pm_fecha_esquejacion'          => optional($data->getPlantaMadre)->pm_fecha_esquejacion, // plantas madres ( Fecha Trasplante )
                'pro_cantidad_plantas_madres'   => $data->pro_cantidad_plantas_madres,
                'estado_lote'                   => $evento,
                'dias_transcurridos'            => $diasTranscurridos,
                'color'                         => $color
            ];
        });

        return response()->json($registros, 200);
    }

    /**
     * Método que Guarda o Actualiza si ya existe registro de plantas madres.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PlantaMadreStoreRequest $request)
    {
        $propagacion = Propagacion::select(['pro_id_lote','pro_cantidad_material'])->where('pro_id_lote', $request->pm_pro_id_lote)->first();
        if (!$propagacion) {
            return response()->json([
                'message' => 'Validación de Datos',
                'errors' => "No existe el lote ingresado $request->pm_pro_id_lote.",
            ], 404);
        }

        $plantaMadre = PlantaMadre::where([
                'pm_pro_id_lote' => $request->pm_pro_id_lote,
                'pm_estado'      => 1
            ]);

        $sumaCantidadBajas = $this->cantidadBajas($request->pm_pro_id_lote, ['ESQUEJES']);

        $nCantidadPlantasPropagadas = ($request->pm_cantidad_semillas + $request->pm_cantidad_esquejes) - $sumaCantidadBajas;
        if ($nCantidadPlantasPropagadas != $propagacion->pro_cantidad_material) {
            return response()->json([
                'message' => 'Validación de Datos',
                'errors'  => "La suma (".$request->pm_cantidad_semillas + $request->pm_cantidad_esquejes.") de Cantidad Esquejes y Cantidad Semilllas debe ser igual a la Cantidad de Material ".($propagacion->pro_cantidad_material - $sumaCantidadBajas)
            ], 409);
        }

        if (count($plantaMadre->get()) == 0) {

            $mensaje = "guardados";

            if (!$this->fnVerificaPermisoUsuario('CREAR')) {
                throw new AuthorizationException;
            }

            $plantaMadre->create([
                'pm_pro_id_lote'        => $request->pm_pro_id_lote,
                'pm_fecha_esquejacion'  => $request->pm_fecha_esquejacion." ".date('H:i:s'),
                'pm_cantidad_semillas'  => $request->pm_cantidad_semillas,
                'pm_cantidad_esquejes'  => $request->pm_cantidad_esquejes,
                'pm_estado'             => true,
            ]);
        }else{

            if (!$this->fnVerificaPermisoUsuario('EDITAR')) {
                throw new AuthorizationException;
            }

            $mensaje = "actualizados";

            $plantaMadreConsulta = $plantaMadre->first();

            // consultando si el lote ya tiene procesos en linea como trasplante a bolsa.
            // Si tiene trasplante a bolsa se restringe que no pueda actualizar el registro,
            // de lo contrario si no tiene procesos si puede editar.
            $trasplantebolsa = Trasplante::select(['tp_id', 'tp_pm_id'])->where([
                    'tp_pm_id'  => $plantaMadreConsulta->pm_id,
                    'tp_tipo'   => "campo",
                    'tp_estado' => 1,
                ])->first();
            if ($trasplantebolsa) {
                return response()->json([
                    'message' => 'Validación de Datos',
                    'errors' => "El Lote[$plantaMadreConsulta->pm_pro_id_lote] ya tiene Trasplante a Bolsa, no se puede editar si ya tiene procesos en Curso.",
                ], 409);
            }

            $plantaMadre->update([
                'pm_pro_id_lote'        => $request->pm_pro_id_lote,
                'pm_fecha_esquejacion'  => $request->pm_fecha_esquejacion." ".date('H:i:s'),
                'pm_cantidad_semillas'  => $request->pm_cantidad_semillas,
                'pm_cantidad_esquejes'  => $request->pm_cantidad_esquejes,
                'pm_estado'             => true,
            ]);
        }

        return response()->json([
            'message' => "Datos $mensaje, para el lote $request->pm_pro_id_lote",
        ], 201);
    }

    /**
     * Busca y Muestra un lote en especifico.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Propagacion $Propagacion)
    {
        $plantaMadre = PlantaMadre::where('pm_pro_id_lote', $Propagacion->pro_id_lote)->get();

        $sumaCantidadBajas = $this->cantidadBajas($Propagacion->pro_id_lote, ['ESQUEJES']);

        // Armando collection con data de propagacion y de plantas madres.
        $esquejeSemilla = collect([
            'pro_id_lote'                 => $Propagacion->pro_id_lote,
            'pro_cantidad_material'       => ($Propagacion->pro_cantidad_material - $sumaCantidadBajas),
            'pm_fecha_esquejacion'        => $plantaMadre->isEmpty() ? '' : substr($plantaMadre[0]->pm_fecha_esquejacion,0,10) ,
            'pm_cantidad_esquejes'        => $plantaMadre->isEmpty() ? 0 : $plantaMadre[0]->pm_cantidad_esquejes,
            'pm_cantidad_semillas'        => $plantaMadre->isEmpty() ? 0 : $plantaMadre[0]->pm_cantidad_semillas
        ]);

        return response()->json([
            'data' => $esquejeSemilla,
        ], 200);
    }
}
