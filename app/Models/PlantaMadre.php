<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlantaMadre extends Model
{
    use HasFactory;

    protected $table = 'planta_madre';
    protected $primaryKey = 'pm_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        "pm_id",
        "pm_pro_id_lote",
        "pm_fecha_esquejacion",
        "pm_cantidad_semillas",
        "pm_cantidad_esquejes",
        "pm_estado",
    ];

    /**
     * Los atributos que deberían estar visibles.
     *
     * @var array
     */
    protected $visible = [
        "pm_id",
        "pm_pro_id_lote",
        "pm_fecha_esquejacion",
        "pm_cantidad_semillas",
        "pm_cantidad_esquejes",
        "pm_estado",
        "getTrasplante",
        "getTrasplantes", // Utilizado para traer todos los trasplantes, (Modulo trasplante bolsa)
        "getPropagacion" // utilizado para modulo de cosecha, tracking.
    ];

    /**
     *
     *
     * @return Illuminate\Support\Collection;
     */
    public function getTrasplante(){
        return $this->belongsTo(Trasplante::class,'pm_id', 'tp_pm_id');
    }

    /**
     *  Obtiene los registros de trasplantes que hacen parte de planta madres.
     *
     * @return Illuminate\Support\Collection;
     */
    public function getTrasplantes(){
        return $this->hasMany(Trasplante::class,'tp_pm_id', 'pm_id');
    }

    /**
     *  Obtiene los registros de Propagacion que hacen parte de planta madres.
     *
     * @return Illuminate\Support\Collection;
     */
    public function getPropagacion(){
        return $this->belongsTo(Propagacion::class,'pm_pro_id_lote', 'pro_id_lote');
    }

    /**
     * Scope para realizar una búsqueda mixta en el módulo de Trasplante.
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param string $buscar Valor a buscar
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeBuscarTrasplante($query, $buscar) {
        if($buscar) {
            return $query
                ->where('pm_pro_id_lote', 'LIKE', "%$buscar%")
                ->orWhere('propagacion.pro_fecha', 'LIKE', "%$buscar%")
                ->orWhere('trasplante.tp_fecha', 'LIKE', "%$buscar%");
        }
    }
}
