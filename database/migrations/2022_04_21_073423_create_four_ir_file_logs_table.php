<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFourIrFileLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('four_ir_file_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('four_ir_initiative_id');
            $table->string('file_path');
            $table->unsignedInteger('module_type')->comment('1=> Project initiation, 2 => Project guideline, 3 => Tna report, 4 => Project cs, 5 => Project curriculum, 6 => CBLM, 7 => Project resource management, 8 => ToT');
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
        Schema::dropIfExists('four_ir_file_logs');
    }
}
