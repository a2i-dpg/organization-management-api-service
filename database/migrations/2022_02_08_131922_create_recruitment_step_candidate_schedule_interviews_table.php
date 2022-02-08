<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecruitmentStepCandidateScheduleInterviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recruitment_step_candidate_schedule_interviews', function (Blueprint $table) {
            $table->id();
            $table->string('applied_job_id');
            $table->unsignedInteger('job_id');
            $table->unsignedInteger('recruitment_step_id');
            $table->unsignedInteger('interview_schedule_id');
            $table->dateTime('invited_at')->nullable();
            $table->unsignedInteger('confirmation_status')->comment("1=>NOT_CONFIRMED,2=>CONFIRMED,3=>REQUEST_RESCHEDULED,4=>ABORTED")->default(1);
            $table->unsignedInteger('is_candidate_present')->comment('1=>true,0=>false')->nullable();
            $table->float('interview_score')->nullable();
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
        Schema::dropIfExists('recruitment_step_candidate_schedule_interviews');
    }
}
