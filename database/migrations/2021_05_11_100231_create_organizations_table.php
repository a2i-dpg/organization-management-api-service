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
            $table->string('title_en', 300);
            $table->string('title_bn', 1000)->nullable();

            $table->unsignedInteger('loc_division_id')->nullable()->index('org_loc_division_id_inx');
            $table->unsignedInteger('loc_district_id')->nullable()->index('org_loc_district_id_inx');
            $table->unsignedInteger('loc_upazila_id')->nullable()->index('org_loc_upazila_id_inx');

            $table->string('address', 1000)->nullable();
            $table->string('mobile', 15)->nullable();
            $table->string('email', 191)->nullable();
            $table->string('fax_no', 30)->nullable();

            $table->string('contact_person_name', 500)->nullable();
            $table->string('contact_person_mobile', 15)->nullable();
            $table->string('contact_person_email', 191)->nullable();
            $table->string('contact_person_designation', 300)->nullable();
            $table->text('description')->nullable();
            $table->text('logo')->nullable();
            $table->string('domain', 191)->nullable();
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
