<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdditionalJobInformationTable extends Migration
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
            $table->string('job_id')->index();
            $table->text("job_responsibilities");
            $table->text("job_responsibilities_en")->nullable();
            $table->text("job_context")->nullable();
            $table->text("job_context_en")->nullable();
            $table->unsignedTinyInteger("job_place_type")->comment("1=> Inside Bangladesh,2=> Outside Bangladesh");
            $table->unsignedDouble('salary_min')->nullable();
            $table->unsignedDouble('salary_max')->nullable();
            $table->unsignedTinyInteger('is_salary_info_show')->comment('1=> Show Salary, 2=> Show Nothing, 3=>Show Negotiable instead of given salary range');
            $table->unsignedTinyInteger('is_salary_compare_to_expected_salary')->comment('Do you want to use this salary to compare with applicants provided expected salary in applicant list')->default(0);
            $table->unsignedTinyInteger('is_salary_alert_excessive_than_given_salary_range')
                ->comment('Do you want to alert applicant while his provided salary is excessive than given salary range at the time of applying');
            $table->unsignedTinyInteger('salary_review')->nullable()->comment('1=>Half Yearly, 2=>Yearly');
            $table->unsignedTinyInteger('festival_bonus')->nullable()->comment('01,02,03,04........');
            $table->text("additional_salary_info")->nullable();
            $table->text("additional_salary_info_en")->nullable();
            $table->unsignedTinyInteger("is_other_benefits")->comment("0=>No, 1=>Yes");
            $table->unsignedTinyInteger('lunch_facilities')->nullable()->comment('1=>Partially Subsidize, 2=>Full Subsidize');
            $table->text('others')->nullable();
            $table->text('others_en')->nullable();
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
        Schema::dropIfExists('additional_job_information');
    }
}
