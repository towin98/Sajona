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
    ];

    /**
     * Scope para realizar una búsqueda mixta en el módulo de Transplante.
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param string $buscar Valor a buscar
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeBuscarTransplante($query, $buscar) {
        if($buscar) {
            return $query
                ->where('pm_pro_id_lote', 'LIKE', "%$buscar%")
                ->orWhere('propagacion.pro_fecha', 'LIKE', "%$buscar%")
                ->orWhere('transplante.tp_fecha', 'LIKE', "%$buscar%");
        }
    }
}
