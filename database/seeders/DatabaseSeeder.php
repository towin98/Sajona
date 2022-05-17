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
        $this->call(PrEstadosCosechaSeeder::class);
        $this->call(PrFaseCultivoSeeder::class);
        $this->call(PrMotivoPerdidaSeeder::class);
        $this->call(PrTipoIncorporacionSeeder::class);
        $this->call(PrTipoLoteSeeder::class);
        $this->call(PrTipoPropagacionSeeder::class);
        $this->call(PrUbicacionSeeder::class);
        $this->call(PrVariedadSeeder::class);
        $this->call(AlertaSeeder::class);

        $this->call(PermissionsSeeder::class);
        for ($i=0; $i < 10; $i++) {
            $propagacion = Propagacion::factory()->create();
            PlantaMadre::create([
                'pm_pro_id_lote'        => $propagacion->pro_id_lote,
                'pm_fecha_esquejacion'  => date('2022-03-02 10:10:12'),
                'pm_cantidad_semillas'  => rand(0,20),
                'pm_cantidad_esquejes'  => rand(0,20),
                'pm_estado'             => true,
            ]);
        }
        // \App\Models\User::factory(10)->create();
    }
}
