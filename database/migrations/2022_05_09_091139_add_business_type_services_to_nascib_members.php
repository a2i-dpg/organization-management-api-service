<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBusinessTypeServicesToNascibMembers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nascib_members', function (Blueprint $table) {
            $table->json("business_type_services")->after("business_type")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nascib_members', function (Blueprint $table) {
            $table->dropColumn('business_type_services');
        });
    }
}
