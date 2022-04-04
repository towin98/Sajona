<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrEstadoCosechaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pr_estado_cosecha', function (Blueprint $table) {
            $table->id("id");
            $table->string("descripcion", 50)->comment('Descripcion de estado de cosecha');
            $table->string("estado",10)      ->comment('Estado de cosecha');
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
        Schema::dropIfExists('pr_estado_cosecha');
    }
}
