<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidateRequirementDegreesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidate_requirement_degrees', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("candidate_requirement_id")->index();
            $table->integer("education_level_id")->nullable();
            $table->integer("edu_group_id")->nullable();
            $table->text("edu_major")->nullable();
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
        Schema::dropIfExists('candidate_requirement_degrees');
    }
}
