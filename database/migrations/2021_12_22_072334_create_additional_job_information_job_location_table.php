<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdditionalJobInformationJobLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('additional_job_information_job_locations', function (Blueprint $table) {
            $table->increments('id');
            $table->string("job_id")->index();
            $table->unsignedInteger('additional_job_information_id')->index('job_locations_additional_job_information_id');
            $table->unsignedMediumInteger('loc_division_id')->nullable();
            $table->unsignedMediumInteger('loc_district_id')->nullable();
            $table->unsignedMediumInteger('loc_upazila_id')->nullable();
            $table->unsignedMediumInteger('loc_union_id')->nullable();
            $table->unsignedMediumInteger('loc_city_corporation_id')->nullable();
            $table->unsignedMediumInteger('loc_city_corporation_ward_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('additional_job_information_job_location');
    }
}
