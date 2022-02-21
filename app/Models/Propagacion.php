<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        "getBaja"
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
     * Obtiene el registro de planta madre asociado a propagación
     *
     * @return Illuminate\Support\Collection;
     */
    public function getPlantaMadre(){
        return $this->belongsTo(PlantaMadre::class,'pro_id_lote', 'pm_pro_id_lote');
    }

    /**
     * Obtiene el registro de bajas asociados a propagación.
     *
     * @return Illuminate\Support\Collection;
     */
    public function getBaja(){
        return $this->hasMany(Baja::class,'bj_pro_id_lote', 'pro_id_lote');
    }

}
