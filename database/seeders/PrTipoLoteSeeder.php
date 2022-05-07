<?php

namespace Database\Seeders;

use App\Models\Parametros\TipoLote;
use Illuminate\Database\Seeder;

class PrTipoLoteSeeder extends Seeder
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
                "nombre"      => "PLANTA MADRE",
                "descripcion" => "",
                "estado"      => "ACTIVO"
            ],
            [
                "nombre"      => "PLANTA COMERCIAL",
                "descripcion" => "",
                "estado"      => "ACTIVO"
            ]
        ];

        foreach ($arrInsert as $key => $insert) {
            TipoLote::create($insert);
        }
    }
}
