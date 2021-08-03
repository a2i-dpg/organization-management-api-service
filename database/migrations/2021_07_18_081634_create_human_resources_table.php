<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHumanResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('human_resources', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('human_resource_template_id')->nullable()->index('human_resources_fk_human_resource_template_id');
            $table->unsignedInteger('organization_id')->index('human_resources_fk_organization_id');
            $table->unsignedInteger('organization_unit_id')->index('human_resources_fk_organization_unit_id');
            $table->string('title_en', 191)->nullable();
            $table->string('title_bn', 191)->nullable();
            $table->unsignedInteger('parent_id')->nullable()->index('human_resources_fk_parent_id')->comment('self parent id');
            $table->unsignedInteger('rank_id')->nullable()->index('human_resources_fk_rank_id');
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->unsignedTinyInteger('is_designation')->default(1)->comment('1 => designation, 0 => wings or section');
            $table->string('skill_ids')->nullable();
            $table->unsignedTinyInteger('row_status')->default(1)->comment('1 => occupied, 2 => vacancy, 0 => inactive');
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('human_resources');
    }
}
