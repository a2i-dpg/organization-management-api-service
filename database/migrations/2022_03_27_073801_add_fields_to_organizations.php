<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToOrganizations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('phone_numbers', 400)->nullable();
            $table->string('mobile_numbers', 400)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn('phone_numbers');
            $table->dropColumn('mobile_numbers');
        });
    }
}
