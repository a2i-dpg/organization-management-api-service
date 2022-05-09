<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyBusinessTypeFieldsToNascibMembers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nascib_members', function (Blueprint $table) {
            $table->string('main_product_name', 600)->nullable()->change();
            $table->text('main_material_description')->nullable()->change();
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
            $table->string('main_product_name', 600);
            $table->text('main_material_description');
        });
    }
}
