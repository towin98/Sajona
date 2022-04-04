<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrTipoIncorporacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pr_tipo_incorporacion', function (Blueprint $table) {
            $table->id("id");
            $table->string("descripcion", 50)->comment('Descripcion de tipo de incorporacion');
            $table->string("estado",10)      ->comment('Estado de tipo de incorporacion');
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
        Schema::dropIfExists('pr_tipo_incorporacion');
    }
}
