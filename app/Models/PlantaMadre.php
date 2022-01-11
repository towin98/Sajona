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
}
