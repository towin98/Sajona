<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCosechaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cosecha', function (Blueprint $table) {
            $table->id("cos_id");
            $table->unsignedBigInteger("cos_tp_id");
            $table->dateTime("cos_fecha_suelo");
            $table->dateTime("cos_fecha_cosecha");
            $table->integer("cos_numero_plantas");
            $table->unsignedBigInteger("cos_estado_cosecha");
            $table->integer("cos_dias_floracion");
            $table->float("cos_peso_verde", 11,2);
            $table->string("cos_observacion", 500)->nullable();
            $table->boolean("cos_estado");
            $table->timestamps();

            $table->foreign('cos_tp_id')->references('tp_id')->on('trasplante');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cosecha');
    }
}
