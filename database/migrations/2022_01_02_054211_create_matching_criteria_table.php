<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchingCriteriaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matching_criteria', function (Blueprint $table) {
            $table->increments('id');
            $table->string("job_id")->index('matching_criteria_job_id_index');
            $table->unsignedTinyInteger("is_age_enabled")->comment("0=>No, 1=>Yes")->nullable();
            $table->unsignedTinyInteger("is_total_year_of_experience_enabled")->comment("0=>No, 1=>Yes")->nullable();
            $table->unsignedTinyInteger("is_gender_enabled")->comment("0=>No, 1=>Yes")->nullable();
            $table->unsignedTinyInteger("is_area_of_experience_enabled")->comment("0=>No, 1=>Yes")->nullable();
            $table->unsignedTinyInteger("is_skills_enabled")->comment("0=>No, 1=>Yes")->nullable();
            $table->unsignedTinyInteger("is_job_location_enabled")->comment("0=>No, 1=>Yes")->nullable();
            $table->unsignedTinyInteger("is_salary_enabled")->comment("0=>No, 1=>Yes")->nullable();
            $table->unsignedTinyInteger("is_area_of_business_enabled")->comment("0=>No, 1=>Yes")->nullable();
            $table->unsignedTinyInteger("is_job_level_enabled")->comment("0=>No, 1=>Yes")->nullable();
            $table->unsignedTinyInteger("is_age_mandatory")->comment("0=>No, 1=>Yes")->nullable();
            $table->unsignedTinyInteger("is_total_year_of_experience_mandatory")->comment("0=>No, 1=>Yes")->nullable();
            $table->unsignedTinyInteger("is_gender_mandatory")->comment("0=>No, 1=>Yes")->nullable();
            $table->unsignedTinyInteger("is_job_location_mandatory")->comment("0=>No, 1=>Yes")->nullable();
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
        Schema::dropIfExists('matching_criteria');
    }
}
