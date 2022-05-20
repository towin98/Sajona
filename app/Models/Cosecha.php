<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cosecha extends Model
{
    use HasFactory;

    protected $table = 'cosecha';
    protected $primaryKey = 'cos_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        "cos_id",
        "cos_tp_id",
        "cos_fecha_suelo",
        "cos_fecha_cosecha",
        "cos_numero_plantas",
        "cos_estado_cosecha",
        "cos_dias_floracion",
        "cos_peso_verde",
        "cos_observacion",
        "cos_estado"
    ];

    /**
     * Los atributos que deberían estar visibles.
     *
     * @var array
     */
    protected $visible = [
        "cos_id",
        "cos_tp_id",
        "cos_fecha_suelo",
        "cos_fecha_cosecha",
        "cos_numero_plantas",
        "cos_estado_cosecha",
        "cos_dias_floracion",
        "cos_peso_verde",
        "cos_observacion",
        "cos_estado",
        "getTrasplanteCampo",
        "getPostCosecha"
    ];

    /**
     *  Obtiene los registros de trasplantes que hacen parte de cosecha.
     *
     * @return Illuminate\Support\Collection;
     */
    public function getTrasplanteCampo(){
        return $this->belongsTo(Trasplante::class,'cos_tp_id', 'tp_id');
    }

    /**
     *  Obtiene los registros de post cosecha que hacen parte de cosecha.
     *
     * @return Illuminate\Support\Collection;
     */
    public function getPostCosecha(){
        return $this->belongsTo(PostCosecha::class,'cos_id', 'post_cos_id');
    }

    /**
     * Scope para realizar una búsqueda mixta en el módulo de Post Cosecha.
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param string $buscar Valor a buscar
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeBuscarPostCosecha($query, $buscar) {
        if($buscar) {
            return $query
                ->whereHas('getTrasplanteCampo', function ($query) use ($buscar) {
                    $query->whereHas('getPlantaMadre', function ($query) use ($buscar) {
                        $query->where('pm_pro_id_lote', 'LIKE', "%$buscar%");
                    });
                })
                ->orWhereHas('getPostCosecha', function ($query) use ($buscar) {
                    $query->where('post_fecha_ini_secado', 'LIKE', "%$buscar%");
                    $query->where('post_fecha_fin_secado', 'LIKE', "%$buscar%"); // Registros no eliminados
                });
        }
    }
}
