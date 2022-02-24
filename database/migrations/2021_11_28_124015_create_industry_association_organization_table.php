<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIndustryAssociationOrganizationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('industry_association_organization', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('industry_association_id');
            $table->unsignedInteger('organization_id');
            $table->string('membership_id', 200);
            $table->unsignedTinyInteger('payment_status')->comment('1=>Success, 2=>Pending, 3=>Cancel, 4=>Fail');
            $table->date('payment_date')->nullable();
            $table->date('renewal_date')->nullable();
            $table->unsignedTinyInteger('row_status')
                ->default(2)
                ->comment('0 => Inactive, 1 => Approved, 2 => Pending, 3 => Rejected');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['industry_association_id', 'organization_id'], 'industry_asso_id_org_id_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('industry_association_organization');
    }
}
