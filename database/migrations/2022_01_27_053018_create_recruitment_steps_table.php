<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecruitmentStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recruitment_steps', function (Blueprint $table) {
            $table->id();
            $table->string('job_id')->index();
            $table->string('title', 300)();
            $table->string('title_en', 150)->nullable();
            $table->unsignedTinyInteger('step_type');
            $table->tinyInteger('is_interview_reschedule_allowed')->comment('1=>true,0=>false')->nullable();
            $table->dateTime('interviewed_details')->nullable();
            $table->dateTime('interview_contact')->nullable();
            $table->unsignedTinyInteger('interview_invite_type')->nullable();
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
        Schema::dropIfExists('recruitment_steps');
    }
}
