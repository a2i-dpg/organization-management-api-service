<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrDemandSkillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_demand_skills', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger("hr_demand_id");
            $table->unsignedTinyInteger("skill_type")->comment('1 => Mandatory, 2 => Optional')->default(1);
            $table->unsignedInteger("skill_id");
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
        Schema::dropIfExists('hr_demand_skills');
    }
}
