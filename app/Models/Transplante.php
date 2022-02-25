<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transplante extends Model
{
    use HasFactory;

    protected $table = 'transplante';
    protected $primaryKey = 'tp_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
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
    ];
}
