<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFourIrAssessmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('four_ir_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('four_ir_project_id');
            $table->string('course_name', 300);
            $table->string('course_name_en', 300)->nullable();
            $table->string('examine_name', 300);
            $table->string('examine_name_en', 300)->nullable();
            $table->string('examiner_name', 300);
            $table->string('examiner_name_en', 300)->nullable();
            $table->string('file_path',300)->nullable();
            $table->string('accessor_type', 100);
            $table->unsignedInteger('accessor_id');
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
        Schema::dropIfExists('four_ir_assessments');
    }
}
