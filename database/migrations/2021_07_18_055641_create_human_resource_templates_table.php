<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHumanResourceTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('human_resource_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('organization_id')->index('human_resource_templates_fk_organization_id');
            $table->string('title_en', 191)->nullable();
            $table->string('title_bn', 191)->nullable();
            $table->unsignedInteger('parent_id')->nullable()->index('human_resource_templates_fk_parent_id')->comment('self parent id');
            $table->unsignedInteger('rank_id')->nullable()->index('human_resource_templates_fk_rank_id');
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->unsignedTinyInteger('is_designation')->default(1)->comment('1 => designation, 0 => wings or section');
            $table->string('skill_ids')->nullable();
            $table->unsignedInteger('organization_unit_type_id')->index('human_resource_templates_fk_organization_unit_type_id');
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
        Schema::dropIfExists('human_resource_templates');
    }
}
