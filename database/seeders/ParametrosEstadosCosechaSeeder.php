<?php

namespace Database\Seeders;

use App\Models\Parametros\EstadoCosecha;
use Illuminate\Database\Seeder;

class ParametrosEstadosCosechaSeeder extends Seeder
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
                "descripcion" => "EN PROCESO",
                "estado"      => "ACTIVO"
            ],
            [
                "descripcion" => "PENDIENTE",
                "estado"      => "ACTIVO"
            ],
            [
                "descripcion" => "FINALIZADO",
                "estado"      => "INACTIVO"
            ],
        ];

        foreach ($arrInsert as $key => $insert) {
            EstadoCosecha::create($insert);
        }
    }
}
