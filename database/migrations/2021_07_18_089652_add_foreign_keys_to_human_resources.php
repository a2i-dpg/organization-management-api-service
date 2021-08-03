<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToHumanResources extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('human_resources', function (Blueprint $table) {
            $table->foreign('human_resource_template_id', 'human_resources_fk_human_resource_template_id')->references('id')->on('human_resource_templates')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->foreign('organization_id', 'human_resources_fk_organization_id')->references('id')->on('organizations')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('organization_unit_id', 'human_resources_fk_organization_unit_id')->references('id')->on('organization_units')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('parent_id', 'human_resources_fk_parent_id')->references('id')->on('human_resources')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('rank_id', 'human_resources_fk_rank_id')->references('id')->on('ranks')->onUpdate('CASCADE')->onDelete('SET NULL');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('human_resources', function (Blueprint $table) {
            $table->dropForeign('human_resources_fk_human_resource_template_id');
            $table->dropForeign('human_resources_fk_organization_id');
            $table->dropForeign('human_resources_fk_organization_unit_id');
            $table->dropForeign('human_resources_fk_parent_id');
            $table->dropForeign('human_resources_fk_rank_id');
        });
    }
}
