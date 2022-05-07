<?php

namespace Database\Seeders;

use App\Models\Parametros\MotivoPerdida;
use Illuminate\Database\Seeder;

class PrMotivoPerdidaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arrInsert = [
            [
                "nombre"      => "PLAGA",
                "descripcion" => "",
                "estado"      => "ACTIVO"
            ],
            [
                "nombre"      => "VERANO",
                "descripcion" => "Mayor intensidad del calor y del sol.",
                "estado"      => "ACTIVO"
            ],
            [
                "nombre"      => "INVIERNO",
                "descripcion" => "Lluvias entre mes de diciembre y marzo.",
                "estado"      => "ACTIVO"
            ]
        ];

        foreach ($arrInsert as $key => $insert) {
            MotivoPerdida::create($insert);
        }
    }
}
