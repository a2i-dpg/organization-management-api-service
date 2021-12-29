<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyInfoVisibilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_info_visibilities', function (Blueprint $table) {
            $table->id();
            $table->string('job_id')->index();
            $table->unsignedTinyInteger('is_company_name_visible')->default(0)->comment("0=> False, 1=> True");
            $table->string('company_name', 600)->nullable();
            $table->string('company_name_en', 300)->nullable();
            $table->unsignedTinyInteger('is_company_address_visible')->default(0)->comment("0=> False, 1=> True");
            $table->unsignedInteger('company_industry_type');
            $table->unsignedTinyInteger('is_company_business_visible')->default(0)->comment("0=> False, 1=> True");
            $table->softDeletes();
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

        Schema::dropIfExists('company_info_visibilities');
    }
}
