<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFourIRInitiativeCsCurriculumCblmTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('four_ir_initiative_cs_curriculum_cblm', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('four_ir_initiative_id');
            $table->unsignedTinyInteger('type')->comment("1 => CS, 2 => Curriculum, 3 => CBLM");
            $table->unsignedTinyInteger('level_from')->nullable();
            $table->unsignedTinyInteger('level_to')->nullable();
            $table->unsignedTinyInteger('approved_by')->comment("1 => NSDA, 2 => BTEB");
            $table->string('developed_organization_name',300);
            $table->string('developed_organization_name_en',300)->nullable();
            $table->string('sector_name',200);
            $table->string('supported_organization_name',200);
            $table->string('supported_organization_name_en',200)->nullable();
            $table->string('file_path', 200)->nullable();
            $table->text('comments')->nullable();
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
        Schema::dropIfExists('four_ir_initiative_cs_curriculum_cblm');
    }
}
