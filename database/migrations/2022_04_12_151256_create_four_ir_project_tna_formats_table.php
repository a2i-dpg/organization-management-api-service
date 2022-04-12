<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFourIRProjectTnaFormatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('four_ir_project_tna_formats', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('four_ir_project_id');
            $table->string('workshop_name');
            $table->string('skill_required');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('venue');
            $table->string('file_path');
            $table->unsignedTinyInteger('row_status')->default(1)->comment('0 => inactive, 1 => active');
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('four_ir_project_tna_formats');
    }
}
