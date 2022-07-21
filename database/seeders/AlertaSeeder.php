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
            "max_rang_propagacion"      => 10,
            "max_rang_bolsa"            => 13,
            "max_rang_campo"            => 15,
            "max_rang_cosecha"          => 30,
            "max_rang_post_cosecha"     => 12
        ]);
    }
}
