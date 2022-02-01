<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIndustryAssociationSkills extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('industry_association_skills', function (Blueprint $table) {
            $table->unsignedInteger('industry_association_id');
            $table->unsignedMediumInteger('skill_id');

            $table->foreign('industry_association_id')
                ->references('id')
                ->on('industry_associations')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table->foreign('skill_id')
                ->references('id')
                ->on('skills')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('industry_association_skills');
    }
}
