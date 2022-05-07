<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrFaseCultivoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pr_fase_cultivo', function (Blueprint $table) {
            $table->id("id");
            $table->string("nombre", 50)->unique()          ->comment('Nombre de fase de cultivo');
            $table->string("descripcion", 50)->nullable()   ->comment('Descripcion de fase de cultivo');
            $table->string("estado",10)                     ->comment('Estado de fase de cultivo');
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
        Schema::dropIfExists('pr_fase_cultivo');
    }
}
