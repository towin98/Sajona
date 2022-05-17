<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlertaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alerta', function (Blueprint $table) {
            $table->id();
            // $table->integer('min_rang_propagacion') ->comment('Rango minimo alerta Propagacion');
            $table->integer('max_rang_propagacion') ->comment('Rango maximo alerta Propagacion');
            // $table->integer('min_rang_bolsa')       ->comment('Rango minimo alerta Bolsa');
            $table->integer('max_rang_bolsa')       ->comment('Rango maximo alerta Bolsa');
            // $table->integer('min_rang_campo')       ->comment('Rango minimo alerta Campo');
            $table->integer('max_rang_campo')       ->comment('Rango maximo alerta Campo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alerta');
    }
}
