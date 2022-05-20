<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyTrasplanteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trasplante', function (Blueprint $table) {
            $table->foreign('tp_tipo_lote', 'fk1_pr_tipo_lote_tp_tipo_lote')->references('id')->on('pr_tipo_lote');
            $table->foreign('tp_ubicacion', 'fk2_pr_ubicacion_tp_ubicacion')->references('id')->on('pr_ubicacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trasplante', function (Blueprint $table) {
            $table->dropForeign('fk1_pr_tipo_lote_tp_tipo_lote');
            $table->dropForeign('fk2_pr_ubicacion_tp_ubicacion');
        });
    }
}
