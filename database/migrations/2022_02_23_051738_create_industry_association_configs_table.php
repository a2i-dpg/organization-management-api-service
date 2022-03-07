<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIndustryAssociationConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('industry_association_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('industry_association_id');
            $table->unsignedTinyInteger('session_type')->comment('1=> JUNE_JULY, 2=> JANUARY_DECEMBER');
            $table->json('payment_gateways')->nullable();
            $table->unsignedTinyInteger('row_status')->default(1)->comment('0 => inactive, 1 => active, 2 => invalid');
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
        Schema::dropIfExists('industry_association_configs');
    }
}
