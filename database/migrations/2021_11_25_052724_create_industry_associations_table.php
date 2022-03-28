<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIndustryAssociationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('industry_associations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('trade_id');

            $table->string('title', 1200);
            $table->string('title_en', 600)->nullable();

            $table->unsignedMediumInteger('loc_division_id')->nullable()
                ->index('org_loc_division_id_inx');
            $table->unsignedMediumInteger('loc_district_id')->nullable()
                ->index('org_loc_district_id_inx');
            $table->unsignedMediumInteger('loc_upazila_id')->nullable()
                ->index('org_loc_upazila_id_inx');

            $table->string('location_latitude', 50)->nullable();
            $table->string('location_longitude', 50)->nullable();
            $table->text('')->nullable();


            $table->string('address', 1200)->nullable();
            $table->string('address_en', 600)->nullable();
            $table->char("country", 3)->default("BD")->comment('ISO Country Code');
            $table->char("phone_code", 5)->default("880")->comment('Country Code for Phone number');
            $table->string('mobile', 15)->nullable();
            $table->string('email', 191)->nullable();
            $table->string('fax_no', 30)->nullable();
            $table->string('trade_number', 100);

            $table->string("name_of_the_office_head", 600)->nullable();
            $table->string("name_of_the_office_head_en", 300)->nullable();
            $table->string("name_of_the_office_head_designation", 600)->nullable();
            $table->string("name_of_the_office_head_designation_en", 300)->nullable();

            $table->string('contact_person_name', 500)->nullable();
            $table->string('contact_person_name_en', 250)->nullable();
            $table->string('contact_person_mobile', 15)->nullable();
            $table->string('contact_person_email', 191)->nullable();
            $table->string('contact_person_designation', 600)->nullable();
            $table->string('contact_person_designation_en', 300)->nullable();

            $table->string('logo', 500)->nullable();
            $table->string('domain', 250)->nullable();

            $table->unsignedTinyInteger('row_status')
                ->default(2)
                ->comment('0 => Inactive, 1 => Approved, 2 => Pending, 3 => Rejected');


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
        Schema::dropIfExists('industry_associations');
    }
}
