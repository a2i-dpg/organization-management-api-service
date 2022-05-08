<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFourIrTnaFormatMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('four_ir_tna_format_methods', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('four_ir_initiative_tna_format_id');
            $table->string('name', 300);
            $table->string('name_en', 300)->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('venue', 500);
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
        Schema::dropIfExists('four_ir_tna_format_methods');
    }
}
