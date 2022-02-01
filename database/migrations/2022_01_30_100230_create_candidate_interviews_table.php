<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidateInterviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidate_interviews', function (Blueprint $table) {
            $table->id();
            $table->string('job_id')->index();
            $table->unsignedInteger('applied_job_id')->index();
            $table->unsignedInteger('recruitment_step_id');
            $table->unsignedTinyInteger('candidate_status')->comment('1=>will come,2=>will not come,3=>Request for reschedule')->nullable();
            $table->unsignedInteger('is_candidate_present')->comment('1=>true,0=>false')->nullable();
            $table->float('interview_score')->nullable();
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
        Schema::dropIfExists('candidate_interviews');
    }
}
