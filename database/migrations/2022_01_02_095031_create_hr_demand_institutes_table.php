<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrDemandInstitutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_demand_institutes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger("hr_demand_id");
            $table->unsignedInteger("institute_id")->nullable();
            $table->unsignedTinyInteger("rejected_by_institute")->default(0);
            $table->unsignedInteger("vacancy_provided_by_institute")->default(0);
            $table->unsignedTinyInteger("rejected_by_industry_association")->default(0);
            $table->unsignedInteger("vacancy_approved_by_industry_association")->default(0);
            $table->unsignedTinyInteger('row_status')->default(1)->comment('0 => inactive, 1 => active, 2 => invalid');
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
        Schema::dropIfExists('hr_demand_institutes');
    }
}
