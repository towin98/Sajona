<?php

namespace Database\Factories;

use App\Models\Propagacion;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropagacionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Propagacion::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "pro_id_lote"                   => rand(1,10000),
            "pro_fecha"                     => date("Y-m-d H:i:s"),
            "pro_tipo_propagacion"          => rand(1,2),
            "pro_variedad"                  => rand(1,2),
            "pro_tipo_incorporacion"        => rand(1,2),
            "pro_cantidad_material"         => 200,
            "pro_cantidad_plantas_madres"   => rand(1,50),
            "pro_estado"                    => true,
        ];
    }
}
