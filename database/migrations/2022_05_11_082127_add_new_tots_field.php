<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddNewTotsField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('four_ir_initiative_tots', function (Blueprint $table) {
            $table->string('co_organiser_name')->after('four_ir_initiative_id');
            $table->string('co_organiser_email')->after('co_organiser_name');
            $table->string('co_organiser_mobile')->after('co_organiser_email');
            $table->string('co_organiser_address')->after('co_organiser_mobile');
            $table->string('co_organiser_address_en')->after('co_organiser_address');

            DB::statement('ALTER TABLE four_ir_initiative_tots CHANGE master_trainer_name organiser_name INT(10) VARCHAR(250)');
            DB::statement('ALTER TABLE four_ir_initiative_tots CHANGE master_trainer_email organiser_email INT(10) VARCHAR(250)');
            DB::statement('ALTER TABLE four_ir_initiative_tots CHANGE master_trainer_mobile organiser_mobile INT(10) VARCHAR(250)');
            DB::statement('ALTER TABLE four_ir_initiative_tots CHANGE master_trainer_address organiser_address INT(10) VARCHAR(250)');
            DB::statement('ALTER TABLE four_ir_initiative_tots CHANGE master_trainer_address_en organiser_address_en INT(10) VARCHAR(250)');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
