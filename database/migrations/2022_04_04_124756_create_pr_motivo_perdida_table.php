<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrMotivoPerdidaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pr_motivo_perdida', function (Blueprint $table) {
            $table->id("id");
            $table->string("descripcion", 50)->comment('Descripcion de motivo de perdida');
            $table->string("estado",10)      ->comment('Estado de motivo de perdida');
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
        Schema::dropIfExists('pr_motivo_perdida');
    }
}
