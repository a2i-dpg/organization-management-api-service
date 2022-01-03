<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidateRequirementProfessionalCertificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidate_requirement_professional_certifications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("candidate_requirement_id")->index('index_prof_cert_can_req_id');
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
        Schema::dropIfExists('candidate_requirement_professional_certifications');
    }
}
