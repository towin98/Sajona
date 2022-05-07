<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrVariedadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pr_variedad', function (Blueprint $table) {
            $table->id("id");
            $table->string("nombre", 50)->unique()          ->comment('Nombre de la variedad');
            $table->string("descripcion", 50)->nullable()   ->comment('Descripcion de la variedad');
            $table->string("estado",10)                     ->comment('Estado de tipo la variedad');
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
        Schema::dropIfExists('pr_variedad');
    }
}
