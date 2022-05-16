<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyBajaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('baja', function (Blueprint $table) {
            $table->foreign('bj_fase_cultivo', 'fk1_pr_fase_cultivo_bj_fase_cultivo')->references('id')->on('pr_fase_cultivo');
            $table->foreign('bj_motivo_perdida', 'fk2_pr_motivo_perdida_bj_motivo_perdida')->references('id')->on('pr_motivo_perdida');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('baja', function (Blueprint $table) {
            $table->dropForeign('fk1_pr_fase_cultivo_bj_fase_cultivo');
            $table->dropForeign('fk2_pr_motivo_perdida_bj_motivo_perdida');
        });
    }
}
