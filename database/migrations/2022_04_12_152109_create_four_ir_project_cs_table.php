<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFourIRProjectCsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('four_ir_project_cs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('four_ir_project_id');
            $table->string('experts',400);
            $table->string('level',300);
            $table->string('approved_by',300);
            $table->string('developer_organization_name',300);
            $table->string('developer_organization_name_en',300)->nullable();
            $table->string('sector_name',200);
            $table->string('sector_name_en',200)->nullable();
            $table->string('supported_by',200);
            $table->text('comment');
            $table->string('file_path');
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
        Schema::dropIfExists('four_ir_project_cs');
    }
}
