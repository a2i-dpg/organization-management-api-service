<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifySectorNameFiledTypeIntegerToFourIrInitiativeCsCurriculumCblm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('four_ir_initiative_cs_curriculum_cblm', function (Blueprint $table) {
            $table->unsignedInteger('sector_name')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('four_ir_initiative_cs_curriculum_cblm', function (Blueprint $table) {
            $table->string('sector_name',200);
        });
    }
}
