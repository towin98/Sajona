<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBajaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('baja', function (Blueprint $table) {
            $table->id("bj_id");
            $table->unsignedBigInteger("bj_pro_id_lote");
            $table->dateTime("bj_fecha");
            $table->integer("bj_cantidad");
            $table->string("bj_fase_cultivo", 20);
            $table->string("bj_observacion", 500);
            $table->string("bj_estado", 20);
            $table->timestamps();

            $table->foreign('bj_pro_id_lote')->references('pro_id_lote')->on('propagacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('baja');
    }
}
