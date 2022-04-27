<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Baja extends Model
{
    use HasFactory;

    protected $table = 'baja';
    protected $primaryKey = 'bj_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        "bj_id",
        "bj_pro_id_lote",
        "bj_fecha",
        "bj_cantidad",
        'bj_fase_cultivo',
        "bj_observacion",
        "bj_estado"
    ];

    /**
     * Los atributos que deberían estar visibles.
     *
     * @var array
     */
    protected $visible = [
        "bj_id",
        "bj_pro_id_lote",
        "bj_fecha",
        "bj_cantidad",
        "bj_fase_cultivo",
        "bj_observacion",
        "bj_estado"
    ];

    static $messages = [
        'bj_fecha.required'                 => 'La fecha de baja es requerida.',
        'bj_fecha.date_format'              => 'La fecha de baja debe cumplir el formato: Y-m-d.',
        'bj_cantidad.integer'               => 'La cantidad de bajas debe ser númerico.',
        'bj_cantidad.required'              => 'La cantidad de bajas es requerida.',
        'bj_fase_cultivo.required'          => 'La Fase del cultivo es requerida.',
        'bj_observacion.max'                => 'La Observación no puede superar los 255 caracteres.',
    ];

    static $rules = [
        'bj_fecha'             => 'required|date_format:Y-m-d',
        'bj_cantidad'          => 'required|integer',
        'bj_fase_cultivo'      => 'required',
        'bj_observacion'       => 'nullable|max:255',
    ];

    /**
     * Scope para realizar una búsqueda mixta.
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param string $buscar Valor a buscar
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeBuscarTransplante($query, $buscar) {
        if($buscar) {
            return $query
                ->where('pm_pro_id_lote', 'LIKE', "%$buscar%")
                ->orWhere('propagacion.pro_fecha', 'LIKE', "%$buscar%");
        }
    }
}
