<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFourIrEmploymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('four_ir_employments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('four_ir_initiative_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('employment_status');
            $table->string('name', 350);
            $table->string('name_en', 350)->nullable();
            $table->string('email', 500);
            $table->string('industry_name', 500);
            $table->string('industry_name_en', 500)->nullable();
            $table->date('job_starting_date');
            $table->string('contact_number', 15);
            $table->string('designation', 350);
            $table->unsignedInteger('starting_salary')->default(0);
            $table->string('medium_of_job', 300);
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
        Schema::dropIfExists('four_ir_employments');
    }
}
