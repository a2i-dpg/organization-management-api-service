<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidateRequirementSkillTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidate_requirement_skill', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("candidate_requirement_id")->index('index_can_skill_can_req_id');
            $table->integer("candidate_requirement_skill")->index('index_can_skill_skill_id');
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
        Schema::dropIfExists('candidate_requirement_skill');
    }
}
