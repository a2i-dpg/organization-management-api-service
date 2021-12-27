<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_infos', function (Blueprint $table) {
            $table->id();
            $table->string('title', 600);
            $table->string('title_en', 300)->nullable();
            $table->unsignedInteger('industry_association_id');
            $table->char("country", 3)->default("BD")->comment('ISO Country Code');
            $table->char("phone_code", 5)->default("880")->comment('Country Code for Phone number');
            $table->string('phone', 20)->nullable();
            $table->string('mobile', 20);
            $table->string('email', 200);
            $table->unsignedTinyInteger('row_status')->default(1)->comment('0 => inactive, 1 => active');
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('contact_us');
    }
}
