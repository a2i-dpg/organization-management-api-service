<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFactoryCategoryIdToNascibMembers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nascib_members', function (Blueprint $table) {
            $table->unsignedInteger('factory_category_id')->after('business_type');
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
            $table->dropColumn('factory_category_id');
        });
    }
}
