<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFourIRInitiativeTotMastersTrainersParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('four_ir_initiative_tot_masters_trainers_participants', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('four_ir_initiative_tot_id');
            $table->string('name', 350);
            $table->string('name_en', 350)->nullable();
            $table->string('mobile', 15);
            $table->string('address', 500);
            $table->string('address_en', 500)->nullable();
            $table->string('email', 500);
            $table->string('organization_name', 500);
            $table->string('organization_name_en', 500)->nullable();
            $table->string('accessor_type', 100);
            $table->unsignedInteger('accessor_id');
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
        Schema::dropIfExists('four_i_r_initiative_tot_masters_trainers_participants');
    }
}
