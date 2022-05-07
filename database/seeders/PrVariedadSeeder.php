<?php

namespace Database\Seeders;

use App\Models\Parametros\Variedad;
use Illuminate\Database\Seeder;

class PrVariedadSeeder extends Seeder
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
                "nombre"      => "VARIEDAD 1",
                "descripcion" => "",
                "estado"      => "ACTIVO"
            ],
            [
                "nombre"      => "VARIEDAD 2",
                "descripcion" => "",
                "estado"      => "ACTIVO"
            ]
        ];

        foreach ($arrInsert as $key => $insert) {
            Variedad::create($insert);
        }
    }
}
