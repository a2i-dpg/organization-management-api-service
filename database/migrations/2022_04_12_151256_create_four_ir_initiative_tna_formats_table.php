<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFourIRInitiativeTnaFormatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('four_ir_initiative_tna_formats', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('four_ir_initiative_id');
            $table->unsignedInteger('method_type')->comment("1 => workshop, 2 => FGD workshop, 3 => Industry visit, 4 => Desktop research, 5 => Existing report review, 6 => others");
            $table->unsignedInteger('workshop_numbers')->default(0);
            $table->string('file_path')->nullable();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('venue');
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
        Schema::dropIfExists('four_ir_initiative_tna_formats');
    }
}
