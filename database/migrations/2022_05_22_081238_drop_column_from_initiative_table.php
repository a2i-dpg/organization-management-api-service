<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnFromInitiativeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('four_ir_initiatives', function (Blueprint $table) {
            $table->dropColumn('implementing_team_launching_date');
            $table->dropColumn('expert_team_launching_date');
            $table->dropColumn('cell_launching_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('initiative', function (Blueprint $table) {
            //
        });
    }
}
