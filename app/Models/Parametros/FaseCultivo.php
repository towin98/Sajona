<?php

namespace App\Models\Parametros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaseCultivo extends Model
{
    use HasFactory;

    protected $table = 'pr_fase_cultivo';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        "descripcion",
        "estado"
    ];

    /**
     * Los atributos que deberían estar visibles.
     *
     * @var array
     */
    protected $visible = [
        "id",
        "descripcion",
        "estado"
    ];

    /**
     * Scope para realizar una búsqueda mixta.
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param string $buscar Valor a buscar
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeBuscar($query, $buscar) {
        if($buscar) {
            return $query
                ->where('id', 'LIKE', "%$buscar%")
                ->orWhere('descripcion', 'LIKE', "%$buscar%")
                ->orWhere('estado', 'LIKE', "%$buscar%");
        }
    }

    /**
     * Scope para ordenar una lista.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $columna Nombnre de la columna de la tabla
     * @param mixed $orden Tipo de ordenamiento ASC|DESC
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdenamiento($query, $columna, $orden){
        if($columna && $orden){
            switch ($columna) {
                case 'id':
                    return $query->orderBy('id', $orden);
                break;
                case 'descripcion':
                    return $query->orderBy('descripcion', $orden);
                break;
                case 'estado':
                    return $query->orderBy('estado', $orden);
                break;
                case 'updated_at':
                    return $query->orderBy('updated_at', $orden);
                break;
            }
        }
    }
}
