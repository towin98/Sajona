<?php

namespace Database\Seeders;

use App\Models\PlantaMadre;
use App\Models\Propagacion;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PermissionsSeeder::class);
        for ($i=0; $i < 10; $i++) {
            $propagacion = Propagacion::factory()->create();
            PlantaMadre::create([
                'pm_pro_id_lote'        => $propagacion->pro_id_lote,
                'pm_fecha_esquejacion'  => date('Y-m-d H:i:s'),
                'pm_cantidad_semillas'  => rand(0,20),
                'pm_cantidad_esquejes'  => rand(0,20),
                'pm_estado'             => true,
            ]);
        }
        $this->call(ParametrosEstadosCosechaSeeder::class);
        \App\Models\User::factory(10)->create();
    }
}
