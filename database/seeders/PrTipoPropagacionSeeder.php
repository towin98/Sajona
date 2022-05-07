<?php

namespace Database\Seeders;

use App\Models\parametros\TipoPropagacion;
use Illuminate\Database\Seeder;

class PrTipoPropagacionSeeder extends Seeder
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
                "nombre"      => "ESQUEJE",
                "descripcion" => "",
                "estado"      => "ACTIVO"
            ],
            [
                "nombre"      => "SEMILLA",
                "descripcion" => "",
                "estado"      => "ACTIVO"
            ],
            [
                "nombre"      => "INVITRO",
                "descripcion" => "",
                "estado"      => "INACTIVO"
            ],
        ];

        foreach ($arrInsert as $key => $insert) {
            TipoPropagacion::create($insert);
        }
    }
}
