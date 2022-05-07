<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFourIrScaleUpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('four_ir_scale_ups', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('four_ir_initiative_id');
            $table->string('project_name', 100);
            $table->string('project_name_en', 100)->nullable();
            $table->decimal('previous_budget')->default(0);
            $table->string('implement_timeline', 400);
            $table->text('implement_area');
            $table->string('project_documents', 400);
            $table->decimal('tentitive_budget')->default(0);
            $table->string('beneficiary_target', 400);
            $table->text('description')->nullable();
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
        Schema::dropIfExists('four_ir_scale_ups');
    }
}
