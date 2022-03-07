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
        "getTransplanteCampo"
    ];

    /**
     *  Obtiene los registros de transplantes que hacen parte de cosecha.
     *
     * @return Illuminate\Support\Collection;
     */
    public function getTransplanteCampo(){
        return $this->belongsTo(Propagacion::class,'cos_tp_id', 'cos_id');
    }
}
