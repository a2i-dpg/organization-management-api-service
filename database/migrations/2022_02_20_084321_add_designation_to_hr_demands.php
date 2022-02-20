<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDesignationToHrDemands extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hr_demands', function (Blueprint $table) {
            $table->string("designation", 250)->after('requirement_en');
            $table->string("designation_en", 100)->nullable()->after('designation');
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
            $table->dropColumn('designation');
            $table->dropColumn('designation_en');
        });
    }
}
