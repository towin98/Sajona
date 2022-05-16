<?php

namespace Database\Factories\Parametros;

use App\Models\Parametros\TipoPropagacion;
use Illuminate\Database\Eloquent\Factories\Factory;

class TipoPropagacionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TipoPropagacion::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "id"            => rand(1,100),
            "nombre"        => $this->faker->text(10),
            "descripcion"   => $this->faker->text(20),
            "estado"        => "ACTIVO",
        ];
    }
}
