<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidateRequirementPreferredEducationalInstitutionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidate_requirement_preferred_educational_institution', function (Blueprint $table) {
            $table->increments('id');
            $table->string("job_id")->index('index_edu_ins_job_id');
            $table->integer("candidate_requirement_id")->index('index_edu_ins_can_req_id');
            $table->integer("preferred_educational_institution_id")->index('index_can_edu_ins_edu_ins_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('candidate_requirement_preferred_educational_institution');
    }
}
