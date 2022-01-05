<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlantaMadreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('planta_madre', function (Blueprint $table) {
            $table->id("pm_id");
            $table->unsignedBigInteger('pm_pro_id_lote');
            $table->dateTime("pm_fecha_esquejacion");
            $table->integer("pm_cantidad_semillas");
            $table->integer("pm_cantidad_esquejes");
            $table->boolean("pm_estado");
            $table->timestamps();

            $table->foreign('pm_pro_id_lote')->references('pro_id_lote')->on('propagacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('planta_madre');
    }
}
