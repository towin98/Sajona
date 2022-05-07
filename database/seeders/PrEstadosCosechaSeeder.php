<?php

namespace Database\Seeders;

use App\Models\Parametros\EstadoCosecha;
use Illuminate\Database\Seeder;

class PrEstadosCosechaSeeder extends Seeder
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
                "nombre"      => "EN PROCESO",
                "descripcion" => "",
                "estado"      => "ACTIVO"
            ],
            [
                "nombre"      => "PENDIENTE",
                "descripcion" => "",
                "estado"      => "ACTIVO"
            ],
            [
                "nombre"      => "FINALIZADO",
                "descripcion" => "",
                "estado"      => "INACTIVO"
            ],
        ];

        foreach ($arrInsert as $key => $insert) {
            EstadoCosecha::create($insert);
        }
    }
}
