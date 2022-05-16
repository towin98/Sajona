<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyCosechaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cosecha', function (Blueprint $table) {
            $table->foreign('cos_estado_cosecha', 'fk1_pr_estado_cosecha_cos_estado_cosecha')->references('id')->on('pr_estado_cosecha');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cosecha', function (Blueprint $table) {
            $table->dropForeign('fk1_pr_estado_cosecha_cos_estado_cosecha');
        });
    }
}
