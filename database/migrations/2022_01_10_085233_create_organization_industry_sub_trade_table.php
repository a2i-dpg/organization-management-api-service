<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationIndustrySubTradeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organization_industry_sub_trade', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('organization_id')->index();
            $table->unsignedInteger('industry_sub_trade_id')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organization_industry_sub_trade');
    }
}
