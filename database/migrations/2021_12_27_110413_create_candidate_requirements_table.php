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
        Schema::create('candidate_requirements', function (Blueprint $table) {
            $table->increments('id');
            $table->string("job_id")->index('candidate_requirement_job_id_index');
            $table->text("other_educational_qualification")->nullable();
            $table->text("other_educational_qualification_en")->nullable();
            $table->unsignedTinyInteger("is_experience_needed")->comment("0=>No, 1=>Yes")->nullable();
            $table->unsignedTinyInteger("is_freshers_encouraged")->comment("0=>No, 1=>Yes")->nullable();
            $table->unsignedTinyInteger("minimum_year_of_experience")->nullable();
            $table->unsignedTinyInteger("maximum_year_of_experience")->nullable();
            $table->text("additional_requirements")->nullable();
            $table->text("additional_requirements_en")->nullable();
            $table->unsignedTinyInteger("age_minimum")->nullable();
            $table->unsignedTinyInteger("age_maximum")->nullable();
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
