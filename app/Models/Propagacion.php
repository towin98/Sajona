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

    /**
     * Reglas de validación para el recurso crear.
     *
     * @var array
     */
    public static $messagesValidators = [
        'pro_fecha.required'                            => 'El campo fecha de propagación es requerido.',
        'pro_fecha.date'                                => 'El tipo de formato del campo fecha de propagación debe ser ej: Y-m-d.',
        'pro_tipo_propagacion.required'                 => 'El campo tipo de propagacion es requerido.',
        'pro_tipo_propagacion.string'                   => 'El campo tipo de propagacion debe ser un string.',
        'pro_variedad.required'                         => 'El campo variedad es requerido.',
        'pro_variedad.numeric'                          => 'El campo variedad debe ser un número.',
        'pro_tipo_incorporacion.required'               => 'El campo tipo incorporacion es requerido.',
        'pro_tipo_incorporacion.string'                 => 'El campo tipo incorporacion debe ser un string.',
        'pro_cantidad_material.required'                => 'El campo cantidad material es requerido.',
        'pro_cantidad_material.numeric'                 => 'El campo cantidad material debe ser un número.',
        'pro_cantidad_plantas_madres.required'          => 'El campo cantidad de plantas madres es requerido.',
        'pro_cantidad_plantas_madres.numeric'           => 'El campo cantidad de plantas madres debe ser un número.',
    ];
}
