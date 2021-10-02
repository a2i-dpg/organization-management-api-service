<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateOrganizationsTable
 */
class CreateOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('organization_type_id')->nullable();
            $table->string('title_en', 500);
            $table->string('title_bn', 1000);
            $table->unsignedMediumInteger('loc_division_id')->nullable()->index('org_loc_division_id_inx');
            $table->unsignedMediumInteger('loc_district_id')->nullable()->index('org_loc_district_id_inx');
            $table->unsignedMediumInteger('loc_upazila_id')->nullable()->index('org_loc_upazila_id_inx');

            $table->string('address', 1000)->nullable();
            $table->char("country", 3)->default("BD")->comment('ISO Country Code');
            $table->char("phone_code", 5)->default("880")->comment('Country Code for Phone number');
            $table->string('mobile', 11);
            $table->string('email', 191);
            $table->string('fax_no', 30)->nullable();

            $table->string("name_of_the_office_head", 300)->nullable();
            $table->string("name_of_the_office_head_designation", 300)->nullable();
            $table->string('contact_person_name', 500)->nullable();
            $table->string('contact_person_mobile', 15)->nullable();
            $table->string('contact_person_email', 191)->nullable();
            $table->string('contact_person_designation', 300)->nullable();

            $table->text('description')->nullable();
            $table->string('logo', 500)->nullable();
            $table->string('domain', 250)->nullable();
            $table->unsignedTinyInteger('row_status')->default(1)->comment('0 => inactive, 1 => active');
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
        Schema::dropIfExists('organizations');
    }
}
