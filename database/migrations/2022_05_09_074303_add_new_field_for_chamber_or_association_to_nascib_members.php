<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFieldForChamberOrAssociationToNascibMembers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nascib_members', function (Blueprint $table) {
            $table->string('chamber_or_association_membership_id', 255)->after('chamber_or_association_code')->nullable();
            $table->date('chamber_or_association_last_membership_renewal_date')->after('chamber_or_association_membership_id');
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
            $table->dropColumn(['chamber_or_association_membership_id', 'chamber_or_association_last_membership_renewal_date']);
        });
    }
}
