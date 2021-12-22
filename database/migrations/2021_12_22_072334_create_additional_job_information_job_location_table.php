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
        Schema::create('additional_job_information_job_location', function (Blueprint $table) {
            $table->unsignedInteger('additional_job_information_id');
            $table->unsignedTinyInteger('loc_division_id');
            $table->unsignedTinyInteger('loc_district_id');
            $table->unsignedTinyInteger('loc_loc_upazila_id');

            $table->foreign('additional_job_information_id',"additional_job_information_id_fk")
                ->references('id')
                ->on('additional_job_information')
                ->onDelete('cascade');
            $table->foreign('loc_division_id','additional_job_information_job_location_division_id_fk')
                ->references('id')
                ->on('loc_divisions')
                ->onDelete('cascade');
            $table->foreign('loc_district_id','additional_job_information_job_location_district_id_fk')
                ->references('id')
                ->on('loc_districts')
                ->onDelete('cascade');
            $table->foreign('loc_upazila_id','employment_status_primary_job_information_upazila_id_fk')
                ->references('id')
                ->on('loc_upazilas')
                ->onDelete('cascade');
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
