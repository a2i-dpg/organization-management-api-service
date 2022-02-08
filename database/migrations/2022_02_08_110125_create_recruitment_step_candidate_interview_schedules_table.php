<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecruitmentStepCandidateInterviewSchedulesTable extends Migration
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
            $table->string('applied_job_id')->index();
            $table->unsignedInteger('job_id')->index();
            $table->unsignedInteger('recruitment_step_id')->index();
            $table->unsignedInteger('interview_schedule_id')->index();
            $table->dateTime('invited_at');
            $table->unsignedInteger('confirmation_status')->comment("0=>NOT_CONFIRMED,1=>CONFIRMED,2=>REQUEST_RESCHEDULED,3=>ABORTED");
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
        Schema::dropIfExists('recruitment_step_candidate_interview_schedules');
    }
}
