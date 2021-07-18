<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToHumanResourceTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('human_resource_templates', function (Blueprint $table) {
            $table->foreign('organization_id', 'human_resource_templates_fk_organization_id')->references('id')->on('organizations')->onUpdate('CASCADE')->onDelete('CASCADE');
//            $table->foreign('organization_unit_type_id', 'human_resource_templates_fk_organization_unit_type_id')->references('id')->on('organization_unit_types')->onUpdate('CASCADE')->onDelete('CASCADE'); //TODO:uncomment after organization unit type done
            $table->foreign('parent_id', 'human_resource_templates_fk_parent_id')->references('id')->on('human_resource_templates')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('rank_id', 'human_resource_templates_fk_rank_id')->references('id')->on('ranks')->onUpdate('CASCADE')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('human_resource_templates', function (Blueprint $table) {
            //
        });
    }
}
