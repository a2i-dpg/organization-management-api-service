<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignCandidatesToSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assign_candidates_to_schedules', function (Blueprint $table) {
            $table->string('applied_job_id')->index();
            $table->unsignedInteger('job_id')->index();
            $table->unsignedInteger('recruitment_step_id')->index();
            $table->unsignedInteger('schedule_id')->index();
            $table->dateTime('invited_at');
            $table->unsignedInteger('confirmation_status')->comment("0=>NOT_CONFIRMED,1=>CONFIRMED,2=>REQUEST_RESCHEDULED,3=>ABORTED");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assign_candidates_to_schedules');
    }
}
