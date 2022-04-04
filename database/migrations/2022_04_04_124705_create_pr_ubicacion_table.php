<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrUbicacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pr_ubicacion', function (Blueprint $table) {
            $table->id("id");
            $table->string("descripcion", 50)->comment('Descripcion de Ubicacion');
            $table->string("estado",10)      ->comment('Estado de Ubicacion');
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
        Schema::dropIfExists('pr_ubicacion');
    }
}
