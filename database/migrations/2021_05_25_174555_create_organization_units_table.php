<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organization_units', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title_en', 191)->nullable();
            $table->string('title_bn', 191)->nullable();
            $table->unsignedInteger('organization_id');
            $table->unsignedInteger('organization_unit_type_id');
//            $table->unsignedInteger('organization_id')->index('organization_units_fk_organization_id');
//            $table->unsignedInteger('organization_unit_type_id')->index('organization_units_fk_organization_unit_type_id');
            $table->unsignedInteger('loc_division_id')->nullable();
            $table->unsignedInteger('loc_district_id')->nullable();
            $table->unsignedInteger('loc_upazila_id')->nullable();
            $table->string('address', 191)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('email', 191)->nullable();
            $table->string('fax_no', 50)->nullable();
            $table->string('contact_person_name', 191)->nullable();
            $table->string('contact_person_mobile', 20)->nullable();
            $table->string('contact_person_email', 191)->nullable();
            $table->string('contact_person_designation', 191)->nullable();
            $table->unsignedInteger('employee_size')->default(0);
            $table->unsignedTinyInteger('row_status')->default(1);
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
        Schema::dropIfExists('organization_units');
    }
}
