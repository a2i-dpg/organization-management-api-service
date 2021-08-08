<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToOrganizationUnitServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('organization_unit_services', function (Blueprint $table) {

            $table->foreign('organization_id', 'org_unit_services_fk_org_id')
                ->references('id')
                ->on('organizations')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table->foreign('organization_unit_id', 'org_unit_services_fk_org_unit_id')
                ->references('id')
                ->on('organization_units')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table->foreign('service_id', 'org_unit_services_fk_service_id')
                ->references('id')
                ->on('services')
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
        Schema::table('organization_unit_services', function (Blueprint $table) {
            $table->dropForeign('organization_unit_services_fk_organization_id');
            $table->dropForeign('organization_unit_services_fk_organization_unit_id');
            $table->dropForeign('organization_unit_services_fk_service_id');
        });
    }
}
