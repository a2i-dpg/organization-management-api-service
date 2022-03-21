<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeIndustryAssociationIdToHrDemandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hr_demands', function (Blueprint $table) {
            $table->unsignedInteger("industry_association_id")->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hr_demands', function (Blueprint $table) {
            $table->unsignedInteger("industry_association_id")->change();
        });
    }
}
