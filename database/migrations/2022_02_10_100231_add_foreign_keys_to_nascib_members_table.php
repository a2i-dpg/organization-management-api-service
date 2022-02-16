<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToNascibMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nascib_members', function (Blueprint $table) {
            $table->foreign('chamber_or_association_loc_district_id', 'nascib_member_fk_chamber_or_association_loc_district_id')
                ->references('id')
                ->on('loc_districts')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table->foreign('organization_loc_district_id', 'nascib_member_fk_organization_loc_district_id')
                ->references('id')
                ->on('loc_districts')
                ->onUpdate('CASCADE')
                ->onDelete('RESTRICT');

            $table->foreign('organization_loc_upazila_id', 'nascib_member_fk_organization_loc_upazila_id')
                ->references('id')
                ->on('loc_upazilas')
                ->onUpdate('CASCADE')
                ->onDelete('RESTRICT');

            $table->foreign('factory_loc_district_id', 'nascib_member_fk_factory_loc_district_id')
                ->references('id')
                ->on('loc_districts')
                ->onUpdate('CASCADE')
                ->onDelete('RESTRICT');

            $table->foreign('factory_loc_upazila_id', 'nascib_member_fk_factory_loc_upazila_id')
                ->references('id')
                ->on('loc_upazilas')
                ->onUpdate('CASCADE')
                ->onDelete('RESTRICT');
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
            $table->dropForeign('nascib_member_fk_chamber_or_association_loc_district_id');
            $table->dropForeign('nascib_member_fk_organization_loc_district_id');
            $table->dropForeign('nascib_member_fk_organization_loc_upazila_id');
            $table->dropForeign('nascib_member_fk_factory_loc_district_id');
            $table->dropForeign('nascib_member_fk_factory_loc_upazila_id');
        });
    }
}
