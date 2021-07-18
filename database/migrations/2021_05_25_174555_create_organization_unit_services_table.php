<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationUnitServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organization_unit_services', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('organization_id')->index('organization_unit_services_fk_organization_id');
            $table->unsignedInteger('organization_unit_id')->index('organization_unit_services_fk_organization_unit_id');
            $table->unsignedInteger('service_id')->index('organization_unit_services_fk_service_id');
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
        Schema::dropIfExists('organization_unit_services');
    }
}
