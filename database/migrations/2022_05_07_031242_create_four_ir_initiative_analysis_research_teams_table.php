<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFourIrInitiativeAnalysisResearchTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('four_ir_initiative_analysis_research_teams', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('four_ir_initiative_analysis_id');
            $table->string('name', 100);
            $table->string('name_en', 100)->nullable();
            $table->string('organization_name', 100);
            $table->string('organization_name_en', 100)->nullable();
            $table->string('designation', 100);
            $table->string('email', 100);
            $table->string('mobile', 15);
            $table->string('accessor_type', 100);
            $table->unsignedInteger('accessor_id');
            $table->unsignedTinyInteger('row_status')->default(1)->comment('0 => inactive, 1 => active');
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('four_ir_initiative_analysis_research_teams');
    }
}
