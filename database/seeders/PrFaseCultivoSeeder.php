<?php

namespace Database\Seeders;

use App\Models\Parametros\FaseCultivo;
use Illuminate\Database\Seeder;

class PrFaseCultivoSeeder extends Seeder
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
                "nombre"      => "ESQUEJES",
                "descripcion" => "",
                "estado"      => "ACTIVO"
            ],
            [
                "nombre"      => "BOLSA",
                "descripcion" => "",
                "estado"      => "ACTIVO"
            ],
            [
                "nombre"      => "CAMPO",
                "descripcion" => "",
                "estado"      => "INACTIVO"
            ],
            [
                "nombre"      => "COSECHA",
                "descripcion" => "",
                "estado"      => "INACTIVO"
            ]
        ];

        foreach ($arrInsert as $key => $insert) {
            FaseCultivo::create($insert);
        }
    }
}
