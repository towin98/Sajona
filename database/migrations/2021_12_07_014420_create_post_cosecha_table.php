<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostCosechaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_cosecha', function (Blueprint $table) {
            $table->id("pos_id");
            $table->unsignedBigInteger("post_cos_id");
            $table->dateTime("post_fecha_ini_secado");
            $table->dateTime("post_fecha_fin_secado");
            $table->integer("post_peso_flor_verde");
            $table->integer("post_peso_biomasa");
            $table->integer("post_peso_flor_seco");
            $table->float("post_cbd", 8,2);
            $table->float("post_thc", 8,2);
            $table->string("post_observacion", 500)->nullable();
            $table->boolean("post_estado");
            $table->timestamps();

            $table->foreign('post_cos_id')->references('cos_id')->on('cosecha');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_cosecha');
    }
}
