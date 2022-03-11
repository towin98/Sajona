<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostCosecha extends Model
{
    use HasFactory;

    protected $table = 'post_cosecha';
    protected $primaryKey = 'pos_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        "post_cos_id",
        "post_fecha_ini_secado",
        "post_fecha_fin_secado",
        "post_peso_flor_verde",
        "post_peso_biomasa",
        "post_peso_flor_seco",
        "post_cbd",
        "post_thc",
        "post_observacion",
        "post_estado"
    ];

    /**
     * Los atributos que deberían estar visibles.
     *
     * @var array
     */
    protected $visible = [
        "pos_id",
        "post_cos_id",
        "post_fecha_ini_secado",
        "post_fecha_fin_secado",
        "post_peso_flor_verde",
        "post_peso_biomasa",
        "post_peso_flor_seco",
        "post_cbd",
        "post_thc",
        "post_observacion",
        "post_estado",
    ];
}
