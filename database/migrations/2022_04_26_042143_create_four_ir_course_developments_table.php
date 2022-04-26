<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFourIRCourseDevelopmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('four_ir_course_developments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('four_ir_project_id');
            $table->string('accessor_type', 100);
            $table->text('training_center_details')->nullable();
            $table->text('training_details')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->date('training_launch_date');
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
        Schema::dropIfExists('four_i_r_course_developments');
    }
}
