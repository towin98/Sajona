<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alerta extends Model
{
    use HasFactory;


    protected $table = 'alerta';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'max_rang_propagacion',
        'max_rang_bolsa',
        'max_rang_campo',
        'max_rang_cosecha',
        'max_rang_post_cosecha'
    ];

    /**
     * Los atributos que deberían estar visibles.
     *
     * @var array
     */
    protected $visible = [
        'id',
        'max_rang_propagacion',
        'max_rang_bolsa',
        'max_rang_campo',
        'max_rang_cosecha',
        'max_rang_post_cosecha'
    ];

}
