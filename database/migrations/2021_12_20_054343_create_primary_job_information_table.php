<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrimaryJobInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('primary_job_information', function (Blueprint $table) {
            $table->increments('id');
            $table->string('job_id');
            $table->unsignedTinyInteger('service_type')->comment("1=>Basic Listing, 2=>Stand-out-listing,3=>Stand Out Premium");
            $table->string("job_title",500);
            $table->string("job_title_en",300)->nullable();
            $table->unsignedMediumInteger("no_of_vacancies")->nullable();
            $table->unsignedInteger("job_category_id");
            $table->date("application_deadline")->comment('Y-m-d');
            $table->unsignedTinyInteger('is_apply_online')->default(0)->comment("0=> False, 1=> True");
            $table->unsignedTinyInteger("resume_receiving_option")->comment('1=>Email, 2=> Hard Copy, 3=>Walk in interview');
            $table->string("email")->nullable()->comment('if select Email then required');
            $table->unsignedTinyInteger("is_use_nise3_mail_system")->default(0);

            $table->text('special_instruction_for_job_seekers')->nullable()->comment("Special Instruction for Job Seekers");
            $table->text('special_instruction_for_job_seekers_en')->nullable();

            $table->text('instruction_for_hard_copy')->nullable()->comment("Instruction for hard copy");
            $table->text('instruction_for_hard_copy_en')->nullable();

            $table->text('instruction_for_walk_in_interview')->nullable()->comment("Instruction for Walk in Interview");
            $table->text('instruction_for_walk_in_interview_en')->nullable();

            $table->unsignedTinyInteger('is_photograph_enclose_with_resume')->default(0)->comment("0=> False, 1=> True");
            $table->unsignedTinyInteger('is_prefer_video_resume')->default(0)->comment("0=> False, 1=> True");
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('primary_job_information');
    }
}
