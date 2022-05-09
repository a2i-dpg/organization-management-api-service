<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyEntrepreneurDateOfBirthToNascibMembers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nascib_members', function (Blueprint $table) {
            $table->date('entrepreneur_date_of_birth')->nullable()->change();
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
            $table->date('entrepreneur_date_of_birth');
        });
    }
}
