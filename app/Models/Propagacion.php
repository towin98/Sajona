<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\parametros\TipoPropagacion;
use App\Models\Parametros\TipoIncorporacion;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Propagacion extends Model
{
    use HasFactory;

    protected $table = 'propagacion';
    protected $primaryKey = 'pro_id_lote';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        "pro_id_lote",
        "pro_fecha",
        "pro_tipo_propagacion",
        "pro_variedad",
        "pro_tipo_incorporacion",
        "pro_cantidad_material",
        "pro_cantidad_plantas_madres",
        "pro_estado",
    ];

    /**
     * Los atributos que deberían estar visibles.
     *
     * @var array
     */
    protected $visible = [
        "pro_id_lote",
        "pro_fecha",
        "pro_tipo_propagacion",
        "pro_variedad",
        "pro_tipo_incorporacion",
        "pro_cantidad_material",
        "pro_cantidad_plantas_madres",
        "pro_estado",
        "getPlantaMadre",
        "getBaja",
        "getTipoPropagacion",
        "getTipoIncorporacion"
    ];

    /**
     * Scope para realizar una búsqueda mixta.
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param string $buscar Valor a buscar
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeBuscar($query, $buscar) {
        if($buscar) {
            return $query
                ->where('pro_id_lote', 'LIKE', "%$buscar%")
                ->orWhere('pro_fecha', 'LIKE', "%$buscar%")
                ->orWhere('pro_tipo_propagacion', 'LIKE', "%$buscar%")
                ->orWhere('pro_tipo_incorporacion', 'LIKE', "%$buscar%")
                ->orWhere('pro_cantidad_material', 'LIKE', "%$buscar%");
        }
    }

    /**
     * Scope para realizar una búsqueda mixta en el modulo de Planta Madre
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param string $buscar Valor a buscar
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeBuscarPLantaMadre($query, $buscar) {
        if($buscar) {
            return $query
                ->where('pro_id_lote', 'LIKE', "%$buscar%")
                ->orWhere('pro_fecha', 'LIKE', "%$buscar%")
                ->orWhere('pro_cantidad_plantas_madres', 'LIKE', "%$buscar%")
                ->orWhereHas('getPlantaMadre', function($queryHas) use ($buscar) {
                    $queryHas->where('pm_fecha_esquejacion', 'LIKE', "%$buscar%");
                });
        }
    }

    /**
     * Scope para realizar una búsqueda mixta en el módulo de Trasplante Bolsa.
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param string $buscar Valor a buscar
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeBuscarTrasplanteBolsa($query, $buscar) {
        if($buscar) {
            return $query
                ->where('pro_id_lote', 'LIKE', "%$buscar%")
                ->orWhere('pro_fecha', 'LIKE', "%$buscar%")
                ->orWhereHas('getPlantaMadre', function ($query) use ($buscar) {
                    $query->whereHas('getTrasplante', function ($query) use ($buscar) {
                        $query->where('tp_fecha', 'LIKE', "%$buscar%");
                    });
                });
        }
    }

    /**
     * Scope para realizar una búsqueda mixta en el módulo de Trasplante Campo.
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param string $buscar Valor a buscar
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeBuscarTrasplanteCampo($query, $buscar) {
        if($buscar) {
            return $query
                ->where('pro_id_lote', 'LIKE', "%$buscar%")
                ->orWhere('pro_fecha', 'LIKE', "%$buscar%")
                ->orWhereHas('getPlantaMadre', function ($query) use ($buscar) {
                    $query->whereHas('getTrasplantes', function ($query) use ($buscar) {
                        $query->where('tp_fecha', 'LIKE', "%$buscar%");
                    });
                });
        }
    }

    /**
     * Obtiene el registro de planta madre asociado a propagación
     *
     * @return Illuminate\Support\Collection;
     */
    public function getPlantaMadre(){
        return $this->belongsTo(PlantaMadre::class,'pro_id_lote', 'pm_pro_id_lote');
    }

    /**
     * Obtiene los registros de bajas asociados a propagación.
     *
     * @return Illuminate\Support\Collection;
     */
    public function getBaja(){
        return $this->hasMany(Baja::class,'bj_pro_id_lote', 'pro_id_lote');
    }

    /**
     * Obtiene el registro de Tipo de Propagación asociado a propagación.
     *
     * @return Illuminate\Support\Collection;
     */
    public function getTipoPropagacion(){
        return $this->belongsTo(TipoPropagacion::class,'pro_tipo_propagacion', 'id');
    }

    /**
     * Obtiene el registro de Tipo incorporacion asociado a propagación
     *
     * @return Illuminate\Support\Collection;
     */
    public function getTipoIncorporacion(){
        return $this->belongsTo(TipoIncorporacion::class,'pro_tipo_incorporacion', 'id');
    }

    /**
     * Scope para ordenar una lista de trasplante a bolsa por una columna determinada.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $columna Nombnre de la columna de la tabla
     * @param mixed $orden Tipo de ordenamiento ASC|DESC
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdenamientoTrasplanteBolsa($query, $columna, $orden){
        if($columna && $orden){
            switch ($columna) {
                case 'id_lote':
                    return $query->orderBy('pro_id_lote', $orden);
                break;
                case 'pro_fecha':
                    return $query->orderBy('pro_fecha', $orden);
                break;
            }
        }
    }

}
