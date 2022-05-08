<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFourIRInitiativeTotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('four_ir_initiative_tots', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('four_ir_initiative_id');
            $table->string('master_trainer_name', 350);
            $table->string('master_trainer_name_en', 350)->nullable();
            $table->string('master_trainer_mobile', 15);
            $table->string('master_trainer_address', 500);
            $table->string('master_trainer_address_en', 500)->nullable();
            $table->string('master_trainer_email', 500);
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
        Schema::dropIfExists('four_i_r_project_tots');
    }
}
