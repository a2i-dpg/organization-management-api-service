<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToOrganizationUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('organization_units', function (Blueprint $table) {
            $table->foreign('organization_id', 'organization_units_fk_organization_id')->references('id')->on('organizations')->onUpdate('CASCADE')->onDelete('RESTRICT');
            $table->foreign('organization_unit_type_id', 'organization_units_fk_organization_unit_type_id')->references('id')->on('organization_unit_types')->onUpdate('CASCADE')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organization_units', function (Blueprint $table) {
            $table->dropForeign('organization_units_fk_organization_id');
            $table->dropForeign('organization_units_fk_organization_unit_type_id');
        });
    }
}
