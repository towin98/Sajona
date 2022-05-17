<?php

namespace Database\Seeders;

use App\Models\Alerta;
use Illuminate\Database\Seeder;

class AlertaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Alerta::create([
            // "min_rang_propagacion"  => 0,
            "max_rang_propagacion"  => 18,
            // "min_rang_bolsa"        => 0,
            "max_rang_bolsa"        => 70,
            // "min_rang_campo"        => 0,
            "max_rang_campo"        => 100,
        ]);
    }
}
