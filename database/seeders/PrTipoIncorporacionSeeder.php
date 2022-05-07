<?php

namespace Database\Seeders;

use App\Models\Parametros\TipoIncorporacion;
use Illuminate\Database\Seeder;

class PrTipoIncorporacionSeeder extends Seeder
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
                "nombre"      => "PROPIA",
                "descripcion" => "",
                "estado"      => "ACTIVO"
            ],
            [
                "nombre"      => "COMPRADA",
                "descripcion" => "",
                "estado"      => "ACTIVO"
            ]
        ];

        foreach ($arrInsert as $key => $insert) {
            TipoIncorporacion::create($insert);
        }
    }
}
