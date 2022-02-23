<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocCityCorporationWardsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('loc_city_corporation_wards', static function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 350);
            $table->string('title_en')->nullable();
            $table->mediumInteger('loc_division_id')->unsigned()->default(0);
            $table->mediumInteger('loc_district_id')->unsigned()->default(0);
            $table->mediumInteger('loc_city_corporation_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();

            /*            $table->foreign('loc_city_corporation_id', 'ccoproward_city_corp_id_foreign')
                            ->references('id')
                            ->on('loc_city_corporations')
                            ->onDelete('CASCADE')
                            ->onUpdate('CASCADE');
            */

        });


    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::drop('loc_city_corporation_wards');

    }

}
