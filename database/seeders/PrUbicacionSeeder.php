<?php

namespace Database\Seeders;

use App\Models\Parametros\Ubicacion;
use Illuminate\Database\Seeder;

class PrUbicacionSeeder extends Seeder
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
                "nombre"      => "CASA  MALLA",
                "descripcion" => "",
                "estado"      => "ACTIVO"
            ],
            [
                "nombre"      => "PLANTA MADRE",
                "descripcion" => "",
                "estado"      => "ACTIVO"
            ]
        ];

        foreach ($arrInsert as $key => $insert) {
            Ubicacion::create($insert);
        }
    }
}
