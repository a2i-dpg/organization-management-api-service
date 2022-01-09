<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdditionalJobInformationOtherBenefitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('additional_job_information_other_benefit', function (Blueprint $table) {
            $table->string('job_id')->index();
            $table->string('additional_job_information_id', 300)->index('additional_job_other_benefit_additional_job_info_id_idx');
            $table->string('other_benefit_id', 300)->index('additional_job_other_benefit_other_benefit_id_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
