<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddtionalAttributeToIndustryAssociationOrganization extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('industry_association_organization', function (Blueprint $table) {
            $table->unsignedTinyInteger('membership_type_id')->after('membership_id')->nullable();
            $table->string('additional_info_model_name')->nullable()->after('membership_type_id');
            $table->unsignedTinyInteger('payment_status')->comment('1=>Success, 2=>Pending, 3=>Cancel, 4=>Fail')->after('membership_type_id');
            $table->date('payment_date')->nullable()->after('membership_type_id');
            $table->date('member_ship_expire_date')->nullable()->after('membership_type_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('industry_association_organization', function (Blueprint $table) {
            $table->dropColumn('membership_type_id');
            $table->dropColumn('additional_info_model_name');
            $table->dropColumn('payment_status');
            $table->dropColumn('payment_date');
            $table->dropColumn('member_ship_expire_date');
        });
    }
}
