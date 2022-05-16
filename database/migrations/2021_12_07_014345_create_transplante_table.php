<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransplanteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transplante', function (Blueprint $table) {
            $table->id("tp_id");
            $table->unsignedBigInteger("tp_pm_id");
            $table->string("tp_tipo", 20);
            $table->unsignedBigInteger("tp_tipo_lote");
            $table->dateTime("tp_fecha");
            $table->unsignedBigInteger("tp_ubicacion");
            $table->float("tp_cantidad_area", 11,2);
            $table->boolean("tp_estado");
            $table->timestamps();

            $table->foreign('tp_pm_id')->references('pm_id')->on('planta_madre');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transplante');
    }
}
