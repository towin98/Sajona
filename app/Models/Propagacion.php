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

}
