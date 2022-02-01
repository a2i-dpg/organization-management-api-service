<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrDemandYouthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_demand_youths', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger("hr_demand_id");
            $table->unsignedInteger("hr_demand_institute_id");
            $table->string("cv_link", '250')->nullable();
            $table->unsignedInteger("youth_id")->nullable();
            $table->unsignedTinyInteger("approval_status")->default(1)->comment('1 => pending, 2 => approved, 2 => rejected');
            $table->unsignedTinyInteger('row_status')->default(1)->comment('0 => inactive, 1 => active, 2 => invalid');
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
        Schema::dropIfExists('hr_demand_youths');
    }
}
