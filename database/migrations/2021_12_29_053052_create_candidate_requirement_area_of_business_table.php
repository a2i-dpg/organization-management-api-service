<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidateRequirementAreaOfBusinessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidate_requirement_area_of_business', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("candidate_requirement_id")->index('index_area_busi_can_req_id');
            $table->integer("area_of_business_id")->index('index_can_area_busi_area_busi_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('candidate_requirement_area_of_business');
    }
}
