<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlertaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alerta', function (Blueprint $table) {
            $table->id();
            $table->integer('max_rang_propagacion')     ->comment('Alerta dia Propagacion');
            $table->integer('max_rang_bolsa')           ->comment('Alerta dia Bolsa');
            $table->integer('max_rang_campo')           ->comment('Alerta dia Campo');
            $table->integer('max_rang_cosecha')         ->comment('Alerta dia cosecha');
            $table->integer('max_rang_post_cosecha')    ->comment('Alerta dia post cosecha');
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
        Schema::dropIfExists('alerta');
    }
}
