<?php

namespace App\Models\Parametros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotivoPerdida extends Model
{
    use HasFactory;

    protected $table = 'pr_motivo_perdida';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        "nombre",
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
        "nombre",
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
                ->orWhere('nombre', 'LIKE', "%$buscar%")
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
                case 'nombre':
                    return $query->orderBy('nombre', $orden);
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

