<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrimaryJobInformationEmploymentStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('primary_job_information_employment_status', function (Blueprint $table) {
            $table->unsignedInteger('primary_job_information_id');
            $table->unsignedTinyInteger('employment_type_id');

            $table->foreign('primary_job_information_id',"primary_job_information_employment_status_pe_job_id_fk")
                ->references('id')
                ->on('primary_job_information')
                ->onDelete('cascade');

            $table->foreign('employment_type_id','employment_status_primary_job_information_pe_job_id_fk')
                ->references('id')
                ->on('employment_types')
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
        Schema::dropIfExists('primary_job_information_employment_status');
    }
}
