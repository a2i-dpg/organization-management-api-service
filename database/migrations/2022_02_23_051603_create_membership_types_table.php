<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembershipTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('membership_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('industry_association_id');
            $table->string('name', 500);
            $table->string('name_en', 191)->nullable();
            $table->unsignedFloat('fee')->nullable();
            $table->unsignedFloat('renewal_fee')->nullable();
            $table->unsignedTinyInteger('payment_nature')->nullable()->comment('1=>DATE_WISE, 2=>SESSION_WISE');
            $table->unsignedTinyInteger('payment_frequency')->nullable()->comment('1=>MONTHLY, 2=>QUARTERLY, 3=>HALF_YEARLY, 4=>YEARLY, 5=>SESSIONAL');
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
        Schema::dropIfExists('membership_types');
    }
}
