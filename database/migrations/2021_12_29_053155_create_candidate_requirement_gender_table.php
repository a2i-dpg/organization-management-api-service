<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidateRequirementGenderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidate_requirement_gender', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("candidate_requirement_id")->index('index_gen_can_req_id');
            $table->integer("gender_id")->index('index_can_gen_gen_id');
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
        Schema::dropIfExists('candidate_requirement_gender');
    }
}
