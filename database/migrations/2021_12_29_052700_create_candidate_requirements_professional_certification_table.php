<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidateRequirementsProfessionalCertificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidate_requirements_professional_certification', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("candidate_requirements_id")->index('candidate_req_prof_cer_candidate_req_id');
            $table->text("professional_certification");
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
        Schema::dropIfExists('candidate_requirements_professional_certification');
    }
}
