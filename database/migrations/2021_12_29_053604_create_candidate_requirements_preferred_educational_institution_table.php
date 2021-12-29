<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidateRequirementsPreferredEducationalInstitutionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidate_requirements_preferred_educational_institution', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("candidate_requirements_id")->index();
            $table->integer("preferred_educational_institution_id")->index();
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
        Schema::dropIfExists('candidate_requirements_preferred_educational_institution');
    }
}
