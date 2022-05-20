<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trasplante extends Model
{
    use HasFactory;

    protected $table = 'trasplante';
    protected $primaryKey = 'tp_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        "tp_id",
        "tp_pm_id",
        "tp_tipo",
        "tp_tipo_lote",
        "tp_fecha",
        "tp_ubicacion",
        "tp_cantidad_area",
        "tp_estado",
    ];


    /**
     * Los atributos que deberían estar visibles.
     *
     * @var array
     */
    protected $visible = [
        "tp_id",
        "tp_pm_id",
        "tp_tipo",
        "tp_tipo_lote",
        "tp_fecha",
        "tp_ubicacion",
        "tp_cantidad_area",
        "tp_estado",
        "getPlantaMadre",
        "getCosecha" // Utilizado en Modulo de cosecha. listar
    ];

    /**
     *
     *
     * @return Illuminate\Support\Collection;
     */
    public function getPlantaMadre(){
        return $this->belongsTo(PlantaMadre::class,'tp_pm_id', 'pm_id');
    }

    /**
     *  Obtiene los registros de cosecha que hacen parte de trasplante a bolsa.
     *
     * @return Illuminate\Support\Collection;
     */
    public function getCosecha(){
        return $this->belongsTo(Cosecha::class,'tp_id', 'cos_tp_id');
    }

    /**
     * Scope para realizar una búsqueda mixta en el módulo de Cosecha.
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param string $buscar Valor a buscar
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeBuscarCosecha($query, $buscar) {
        if($buscar) {
            return $query
                ->where('tp_fecha', 'LIKE', "%$buscar%")
                ->orWhereHas('getPlantaMadre', function ($query) use ($buscar) {
                    $query->where('pm_pro_id_lote', 'LIKE', "%$buscar%");

                    $query->orWhereHas('getPropagacion', function ($query) use ($buscar) {
                        $query->where('pro_cantidad_plantas_madres', 'LIKE', "%$buscar%");
                    });
                })
                ->orWhereHas('getCosecha', function ($query) use ($buscar) {
                    $query->where('cos_fecha_cosecha', 'LIKE', "%$buscar%");
                    $query->where('cos_estado',1); // Registros no eliminados
                });
        }
    }
}
