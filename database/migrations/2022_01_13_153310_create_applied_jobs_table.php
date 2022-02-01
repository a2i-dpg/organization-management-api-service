<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppliedJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applied_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('job_id')->index();
            $table->integer('youth_id')->index();
            $table->unsignedInteger('apply_status')->index();
            $table->unsignedInteger('current_step_id');
            $table->dateTime('applied_at')->nullable();
            $table->dateTime('profile_viewed_at')->nullable();
            $table->integer('expected_salary')->nullable();
            $table->dateTime('hire_invited_at')->nullable();
            $table->dateTime('hired_at')->nullable();
//            $table->unsignedTinyInteger('interview_invite_source')->nullable();
            $table->unsignedTinyInteger('hire_invite_type')->nullable();
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
        Schema::dropIfExists('applied_jobs');
    }
}
