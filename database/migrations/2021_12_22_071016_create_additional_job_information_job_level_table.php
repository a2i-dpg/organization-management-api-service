<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdditionalJobInformationJobLevelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('additional_job_information_job_levels', function (Blueprint $table) {
            $table->increments('id');
            $table->string("job_id")->index();
            $table->unsignedInteger('additional_job_information_id')->index('job_level_additional_job_information_id');
            $table->unsignedTinyInteger('job_level_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('additional_job_information_job_level');
    }
}
