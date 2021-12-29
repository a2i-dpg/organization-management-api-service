<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidateRequirementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('additional_job_information', function (Blueprint $table) {
            $table->increments('id');
            $table->string("job_id");//->unique();
            $table->unsignedTinyInteger("minimum_year_of_experience")->comment("")->nullable();
            $table->unsignedTinyInteger("maximum_year_of_experience")->comment("")->nullable();
            $table->unsignedTinyInteger("freshers")->comment("0=>No, 1=>Yes")->nullable();
            $table->text("additional_requirements")->comment("Additional requirements");
            $table->unsignedTinyInteger("age_minimum")->comment('01,02,03,04........')->nullable();
            $table->unsignedTinyInteger("age_maximum")->comment('01,02,03,04........')->nullable();
            $table->unsignedTinyInteger("person_with_disability")->comment("0=>No, 1=>Yes");
            $table->unsignedTinyInteger("preferred_retired_army_officer")->comment("0=>No, 1=>Yes");
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
        Schema::dropIfExists('candidate_requirements');
    }
}
