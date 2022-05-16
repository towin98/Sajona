<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyPropagacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('propagacion', function (Blueprint $table) {
            $table->foreign('pro_tipo_propagacion', 'fk1_pr_tipo_propagacion_pro_tipo_propagacion')->references('id')->on('pr_tipo_propagacion');
            $table->foreign('pro_variedad', 'fk2_pr_variedad_pro_variedad')->references('id')->on('pr_variedad');
            $table->foreign('pro_tipo_incorporacion', 'fk3_pr_tipo_incorporacion_pro_tipo_incorporacion')->references('id')->on('pr_tipo_incorporacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('propagacion', function (Blueprint $table) {
            $table->dropForeign('fk1_pr_tipo_propagacion_pro_tipo_propagacion');
            $table->dropForeign('fk2_pr_variedad_pro_variedad');
            $table->dropForeign('fk3_pr_tipo_incorporacion_pro_tipo_incorporacion');
        });
    }
}
