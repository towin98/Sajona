<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropagacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('propagacion', function (Blueprint $table) {
            $table->id('pro_id_lote');
            $table->dateTime("pro_fecha");
            $table->integer("pro_tipo_propagacion");
            $table->integer("pro_variedad");
            $table->integer("pro_tipo_incorporacion");
            $table->integer("pro_cantidad_material");
            $table->integer("pro_cantidad_plantas_madres");
            $table->boolean("pro_estado");
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
        Schema::dropIfExists('propagacion');
    }
}
